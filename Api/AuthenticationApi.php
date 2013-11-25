<?php
/**
 * OAuth.
 *
 * @copyright Christian Flach (Cmfcmf)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Christian Flach <cmfcmf.flach@gmail.com>.
 * @link https://www.github.com/cmfcmf/OAuth
 * @link http://zikula.org
 */

namespace Cmfcmf\OAuthModule\Api;

use Cmfcmf\OAuthModule\Entity\MappedIdEntity;
use Cmfcmf\OAuthModule\Entity\UserEntity;
use Cmfcmf\OAuthModule\Helper\AuthenticationMethod;
use Cmfcmf\OAuthModule\Helper\Builder;
use Cmfcmf\OAuthModule\Provider\AbstractOAuth1Provider;
use Cmfcmf\OAuthModule\SymfonySession;
use Cmfcmf\OAuthModule\Util\WorkflowUtil;
use LogUtil;
use ModUtil;
use ServiceUtil;
use SessionUtil;
use Symfony\Component\Debug\Exception\FatalErrorException;
use System;
use Zikula_Api_AbstractAuthentication;
use Cmfcmf\OAuthModule\Util\ProviderUtil;

/**
 * The user authentication services for the log-in process through the OAuth protocol.
 */
class AuthenticationApi extends Zikula_Api_AbstractAuthentication
{
    /**
     * The list of valid authentication methods that this module supports.
     *
     * This list is meant to be immutable, therefore it would be prudent to
     * only expose copies, and unwise to expose explicit references.
     *
     * @var AuthenticationMethod[]
     */
    protected $authenticationMethods = array();

    /**
     * Initialize the API instance with the list of valid authentication methods supported.
     *
     * @return void
     */
    protected function  postInitialize() {
        parent::postInitialize();

        $oAuthProvider = ProviderUtil::getAllOAuthProvider();

        /** @var \Cmfcmf\OAuthModule\Provider\AbstractOAuthProvider $provider */
        foreach ($oAuthProvider as $provider) {
            $authenticationMethod = new AuthenticationMethod(
                $this->name,
                $provider->getProviderName(),
                $provider->getShortDescription(),
                $provider->getLongDescription(),
                true,
                $provider->getIcon()
            );
            $this->authenticationMethods[$provider->getProviderName()] = $authenticationMethod;

            // Enabled method if modvar allows it.
            $authenticationMethod->tryToEnableForAuthentication();
            $authenticationMethod->tryToEnableForRegistration();
        }
    }

    /**
     * Informs the calling function whether this authmodule is reentrant or not.
     *
     * The OAuth for Zikula module is reentrant. It must redirect to the OAuth provider for authorization.
     *
     * @return bool true.
     */
    public function isReentrant()
    {
        return true;
    }

    /**
     * Indicate whether this module supports the indicated authentication method.
     *
     * Parameters passed in $args:
     * ---------------------------
     * string $args['method'] The name of the authentication method for which support is enquired.
     *
     * @param array $args All arguments passed to this function, see above.
     *
     * @return boolean True if the indicated authentication method is supported by this module; otherwise false.
     *
     * @throws \InvalidArgumentException Thrown if invalid parameters are sent in $args.
     */
    public function supportsAuthenticationMethod(array $args)
    {
        if (isset($args['method']) && is_string($args['method'])) {
            $methodName = $args['method'];
        } else {
            throw new \InvalidArgumentException($this->__('An invalid \'method\' parameter was received.'));
        }

        $isSupported = (bool)isset($this->authenticationMethods[$methodName]);

        return $isSupported;
    }

    /**
     * Indicates whether a specified authentication method that is supported by this module is enabled for use.
     *
     * Parameters passed in $args:
     * ---------------------------
     * string $args['method'] The name of the authentication method for which support is enquired.
     *
     * @param array $args All arguments passed to this function, see above.
     *
     * @return boolean True if the indicated authentication method is enabled by this module; otherwise false.
     *
     * @throws \InvalidArgumentException Thrown if invalid parameters are sent in $args.
     */
    public function isEnabledForAuthentication(array $args)
    {
        if (isset($args['method']) && is_string($args['method'])) {
            if (isset($this->authenticationMethods[$args['method']])) {
                $authenticationMethod = $this->authenticationMethods[$args['method']];
            } else {
                throw new \InvalidArgumentException($this->__f('An unknown method (\'%1$s\') was received.', array($args['method'])));
            }
        } else {
            throw new \InvalidArgumentException($this->__('An invalid \'method\' parameter was received.'));
        }

        return $authenticationMethod->isEnabledForAuthentication();
    }

    /**
     * Retrieves an array of authentication methods defined by this module, possibly filtered by only those that are enabled.
     *
     * Parameters passed in $args:
     * ---------------------------
     * integer $args['filter'] Either {@link FILTER_ENABLED} (value 1), {@link FILTER_NONE} (value 0), or not present; allows the result to be filtered.
     *                              If this argument is FILTER_ENABLED, then only those authentication methods that are also enabled are returned.
     *
     * @param array $args All arguments passed to this function.
     *
     * @return array An array containing the authentication methods defined by this module, possibly filtered by only those that are enabled.
     *
     * @throws \InvalidArgumentException Thrown if invalid parameters are sent in $args.
     */
    public function getAuthenticationMethods(array $args = null)
    {
       if (isset($args) && isset($args['filter'])) {
            if (is_numeric($args['filter']) && ((int)$args['filter'] == $args['filter'])) {
                switch($args['filter']) {
                    case Zikula_Api_AbstractAuthentication::FILTER_NONE:
                    case Zikula_Api_AbstractAuthentication::FILTER_ENABLED:
                    case Zikula_Api_AbstractAuthentication::FILTER_REGISTRATION_ENABLED:
                        $filter = $args['filter'];
                        break;
                    default:
                        throw new \InvalidArgumentException($this->__f('An unknown value for the \'filter\' parameter was received (\'%1$d\').', array($args['filter'])));
                        break;
                }
            } else {
                throw new \InvalidArgumentException($this->__f('An invalid value for the \'filter\' parameter was received (\'%1$s\').', array($args['filter'])));
            }
        } else {
            $filter = Zikula_Api_AbstractAuthentication::FILTER_NONE;
        }

        switch ($filter) {
            case Zikula_Api_AbstractAuthentication::FILTER_ENABLED:
                $authenticationMethods = array();
                foreach ($this->authenticationMethods as $authenticationMethod) {
                    if ($authenticationMethod->isEnabledForAuthentication()) {
                        $authenticationMethods[$authenticationMethod->getMethod()] = $authenticationMethod;
                    }
                }
                break;
            case Zikula_Api_AbstractAuthentication::FILTER_REGISTRATION_ENABLED:
                $authenticationMethods = array();
                foreach ($this->authenticationMethods as $authenticationMethod) {
                    if ($authenticationMethod->isEnabledForRegistration()) {
                        $authenticationMethods[$authenticationMethod->getMethod()] = $authenticationMethod;
                    }
                }
                break;
            default:
                $authenticationMethods = $this->authenticationMethods;
                break;
        }

        return $authenticationMethods;
    }

    /**
     * Retrieves an authentication method defined by this module.
     *
     * Parameters passed in $args:
     * ---------------------------
     * string 'method' The name of the authentication method.
     *
     * @param array $args All arguments passed to this function.
     *
     * @return array An array containing the authentication method requested.
     *
     * @throws \InvalidArgumentException Thrown if invalid parameters are sent in $args.
     */
    public function getAuthenticationMethod(array $args)
    {
        if (!isset($args['method'])) {
            throw new \InvalidArgumentException($this->__f('An invalid value for the \'method\' parameter was received (\'%1$s\').', array($args['method'])));
        }

        if (!isset($this->authenticationMethods[($args['method'])])) {
            throw new FatalErrorException($this->__f('The requested authentication method \'%1$s\' does not exist.', array($args['method'])));
        }

        return $this->authenticationMethods[($args['method'])];
    }

    /**
     * Registers a user account record or a user registration request with the authentication method.
     *
     * This is called during the user registration process to associate an authentication method provided by this authentication module
     * with a user (either a full user account, or a user's registration request).
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * array   'authentication_method' An array identifying the authentication method to associate with the user account or registration
     *                                      record. The array should contain two elements: 'modname' containing the authentication module's
     *                                      name (the name of this module), and 'method' containing the name of an authentication method
     *                                      defined by this module.
     * array   'authentication_info'   An array containing the authentication information for the user. For the OAuth module, this should
     *                                      contain the user's supplied id and claimed id.
     * numeric 'uid'                   The user id of the user account record or registration request to associate with the authentication method and
     *                                      authentication information.
     *
     * @param array $args All parameters passed to this function.
     *
     * @return boolean True if the user account or registration request was successfully associated with the authentication method and
     *                      authentication information; otherwise false.
     *
     * @throws \InvalidArgumentException Thrown if the arguments array is invalid, or the user id, authentication method, or authentication information
     *                                      is invalid.
     */
    public function register(array $args)
    {
        if (!isset($args['uid']) || empty($args['uid']) || !is_numeric($args['uid']) || ((string)((int)$args['uid']) != $args['uid'])) {
            throw new \InvalidArgumentException($this->__('Invalid user id has been received.'));
        }

        $uid = $args['uid'];

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new \InvalidArgumentException($this->__('Invalid authentication information has been received.'));
        }

        $authenticationInfo = $args['authentication_info'];
        if (!isset($authenticationInfo['claimed_id']) || empty($authenticationInfo['claimed_id'])) {
            throw new \InvalidArgumentException($this->__('Invalid authentication information has been received. A claimed ID was not specified.'));
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            throw new \InvalidArgumentException($this->__('Invalid authentication method has been received.'));
        }

        $authenticationMethod = $args['authentication_method'];
        if (!isset($authenticationMethod['modname']) || empty($authenticationMethod['modname'])
                || !isset($authenticationMethod['method']) || empty($authenticationMethod['method'])
                ) {
            throw new \InvalidArgumentException($this->__('Invalid authentication method has been received. Either an authentication module name or a method name was missing.'));
        }

        $OAuthHelper = Builder::buildInstance($authenticationMethod['method']);
        if ($OAuthHelper == false) {
            throw new FatalErrorException($this->__('The authentication method \'%1$s\' does not appear to be supported by the authentication module \'%2$s\'.', array($authenticationMethod['method'], $authenticationMethod['modname'])));
        }

        $claimedID = $authenticationInfo['claimed_id'];

        $thisUserCount = count(ModUtil::apiFunc($this->getName(), 'selection', 'getEntities', array('where' => 'claimedId:eq:' . $claimedID . ',userId:eq:' . $uid)));
        if ($thisUserCount === false) {
            throw new FatalErrorException($this->__f('Internal error: Unable to check for duplicate claimed id for %1$s = %2$s', array('uid', $uid)));
        }
        $otherUserCount = count(ModUtil::apiFunc($this->getName(), 'selection', 'getEntities', array('where' => 'claimedId:eq:' . $claimedID)));
        if ($otherUserCount === false) {
            throw new FatalErrorException($this->__('Internal error: Unable to check for duplicate claimed id across all users'));
        }

        if ($thisUserCount > 0) {
            $this->registerError($this->__f('The claimed OAuth \'%1$s\' is already associated with the specified user account.', $claimedID));
            return false;
        } elseif ($otherUserCount > 0) {
            $this->registerError($this->__f('The claimed OAuth \'%1$s\' is already associated with another account. If this is your OAuth, then please contact the site administrator.', $claimedID));
            return false;
        } else {
            try {
                $workflowHelper = new WorkflowUtil($this->serviceManager, ModUtil::getModule($this->name));

                $mappedId = new MappedIdEntity();
                $mappedId->setUserId($uid);
                $mappedId->setProvider($authenticationMethod['method']);
                $mappedId->setClaimedId($claimedID);
                $mappedId->setWorkflowState('approved');
                $mappedId->set_bypassValidation(true);
                $mappedId->setCreatedUserId($uid);
                $mappedId->setUpdatedUserId($uid);

                $workflowHelper->executeAction($mappedId, 'submitŝ');
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    /**
     * @var array We need to cache the results of the 'internalCheckPassword()' function, as this function can be
     * called multiple times during one request, but the OAuth authentication check only works once.
     */
    private $internalCheckPasswordResults = array();

    /**
     * Authenticates authentication info with the authenticating source.
     *
     * ATTENTION: Any function that causes this function to be called MUST specify a return URL, and therefore
     * must be reentrant. In other words, in order to call this function, there must exist a controller function
     * (specified by the return URL) that the OAuth server can return to, and that function must restore the
     * pertinent state for the user as if he never left this site! Session variables should be used to store all
     * pertinent variables, and those must be re-read into the user's state when the return URL is called back
     * by the OAuth server.
     *
     * Note that, despite this function's name, there is no requirement that a password be part of the authentication info.
     * Merely that enough information be provided in the authentication info array to unequivocally authenticate the user. For
     * most authenticating authorities this will be the equivalent of a user name and password, but--again--there
     * is no restriction here. This is not, however, a "user exists in the system" function. It is expected that
     * the authenticating authority validate what ever is used as a password or the equivalent thereof.
     *
     * This function makes no attempt to match the given authentication info with a Zikula user id (uid). It simply asks the
     * authenticating authority to authenticate the authentication info provided. No "login" should take place as a result of
     * this authentication.
     *
     * @param array   $authenticationMethod An array identifying the selected authentication method by 'modname' and 'method'.
     * @param array   $authenticationInfo   An array containing the authentication information supplied by the user; for this module, a 'supplied_id'.
     * @param string  $reentrantURL         The URL to which an external OAuth Provider should return in order to reenter the authentication proces
     *                                          following a user's attempt to authenticate on the external server.
     * @param boolean $forRegistration      If true, then a simple registration request extension will be added to the OAuth authentication request,
     *                                          asking for the user's nickname and email address.
     *
     * @return array|boolean If the authentication info authenticates with the source, then an array containing the 'claimed_id', and any optional
     *                              simple registration fields; otherwise false on authentication failure or error.
     *
     * @throws \InvalidArgumentException
     */
    protected function internalCheckPassword(array $authenticationMethod, array $authenticationInfo, $reentrantURL = null, $forRegistration = false)
    {
        $hash = md5(serialize($authenticationMethod) . serialize($authenticationInfo) . $reentrantURL . $forRegistration);
        if (isset($this->internalCheckPasswordResults[$hash])) {
            return $this->internalCheckPasswordResults[$hash];
        }

        /** @var \Cmfcmf\OAuthModule\Provider\AbstractOAuthProvider $OAuthHelper */
        $OAuthHelper = Builder::buildInstance($authenticationMethod['method']);
        if (!isset($OAuthHelper) || ($OAuthHelper === false)) {
            throw new \InvalidArgumentException($this->__('The specified authentication method appears to be unsupported.'));
        }

        if (!isset($reentrantURL) || empty($reentrantURL)) {
            throw new \InvalidArgumentException($this->__('An invalid reentrant_url was recieved.'));
        }

        if ($forRegistration && $this->getVar('useMaximumInformationForRegistration', true)) {
            $scopes = $OAuthHelper->getScopesMaximum();
        } else {
            $scopes = $OAuthHelper->getScopesMinimum();
        }

        if (!is_array($scopes)) {
            throw new FatalErrorException($this->__f('Invalid scopes for the %s OAuth provider received.', $OAuthHelper->getOAuthServiceName()));
        }

        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = \ServiceUtil::get('request');
        $storage = new SymfonySession($request->getSession(), false);

        /** @var \OAuth\Common\Service\AbstractService $service  */
        $service = $OAuthHelper->createService($storage, $scopes, $reentrantURL);

        $oAuthType = ($OAuthHelper instanceof AbstractOAuth1Provider) ? 1 : 2;

        $code = $this->request->query->get('code', false);
        $error = $this->request->query->get('error', false);
        $oAuthToken = $this->request->query->get('oauth_token', false);
        $oAuthVerifier = $this->request->query->get('oauth_verifier', false);

        if ($code || $error || $oAuthToken || $oAuthVerifier) {
            $reentrantURL = $this->request->getSession()->get('reentrant_url', '', 'OAuth_Authentication_checkPassword');
            $this->request->getSession()->clearNamespace('OAuth_Authentication_checkPassword');

            if (($code && $oAuthType === 2) || ($oAuthToken && $oAuthVerifier && $oAuthType === 1)) {
                if ($oAuthType === 1) {
                    // OAuth 1
                    /** @var \Cmfcmf\OAuthModule\Provider\AbstractOAuth1Provider $OAuthHelper */
                    $token = $storage->retrieveAccessToken($OAuthHelper->getOAuthServiceName());

                    /** @var \OAuth\OAuth1\Token\StdOAuth1Token $token */
                    /** @var \OAuth\OAuth1\Service\AbstractService $service */
                    $token = $service->requestAccessToken(
                        $oAuthToken,
                        $oAuthVerifier,
                        $token->getRequestTokenSecret()
                    );
                    $claimedId = $OAuthHelper->extractClaimedIdFromToken($token);
                } else {
                    // OAuth 2
                    /** @var \OAuth\Common\Token\TokenInterface $token */
                    /** @var \OAuth\OAuth2\Service\AbstractService $service */
                    $token = $service->requestAccessToken($code);
                    $claimedId = $token->getAccessToken();
                }

                $returnResult = array(
                    'claimed_id' => $claimedId
                );

                if ($forRegistration) {
                    $returnResult = array_merge($returnResult, $OAuthHelper->getAdditionalInformationForRegistration($service));
                }

                // Cache result.
                $this->internalCheckPasswordResults[$hash] = $returnResult;

                return $returnResult;
            } else if ($error) {
                switch($error) {
                    case 'access_denied':
                        $this->registerError($this->__('OAuth authorization was canceled by you.'));
                        break;
                    default:
                        $this->registerError($this->__('OAuth authorization failed due to an unknown error.'));
                        break;
                }
                return false;
            }
        } else {
            SessionUtil::requireSession();
            $this->request->getSession()->clearNamespace('OAuth_Authentication_checkPassword');
            $this->request->getSession()->set('reentrant_url', $reentrantURL, 'OAuth_Authentication_checkPassword');

            if ($oAuthType === 1) {
                // OAuth 1
                /** @var \OAuth\OAuth1\Service\AbstractService $service */
                $token = $service->requestRequestToken();
                $url = $service->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
            } else {
                // OAuth 2
                $url = $service->getAuthorizationUri();
            }
            header('Location: ' . $url);
            exit;
        }

        return false;
    }

    /**
     * Authenticates authentication info with the authenticating source, returning a simple boolean result.
     *
     * ATTENTION: Any function that causes this function to be called MUST specify a return URL, and therefore
     * must be reentrant. In other words, in order to call this function, there must exist a controller function
     * (specified by the return URL) that the OAuth server can return to, and that function must restore the
     * pertinent state for the user as if he never left this site! Session variables should be used to store all
     * pertinent variables, and those must be re-read into the user's state when the return URL is called back
     * by the OAuth server.
     *
     * Note that, despite this function's name, there is no requirement that a password be part of the authentication info.
     * Merely that enough information be provided in the authentication info array to unequivocally authenticate the user. For
     * most authenticating authorities this will be the equivalent of a user name and password, but--again--there
     * is no restriction here. This is not, however, a "user exists in the system" function. It is expected that
     * the authenticating authority validate what ever is used as a password or the equivalent thereof.
     *
     * This function makes no attempt to match the given authentication info with a Zikula user id (uid). It simply asks the
     * authenticating authority to authenticate the authentication info provided. No "login" should take place as a result of
     * this authentication.
     *
     * This function may be called to initially authenticate a user during the registration process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from authenticateUser() in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid).
     *
     * This function differs from login()  in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid). In addition this function makes no attempt to
     * perform any login-related processes on the authenticating system.
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return boolean True if the authentication info authenticates with the source; otherwise false on authentication failure or error.
     *
     * @throws \InvalidArgumentException
     */
    public function checkPassword(array $args)
    {
        $passwordAuthenticates = false;

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new \InvalidArgumentException($this->__('An invalid set of authentication information was received.'));
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            throw new \InvalidArgumentException($this->__('An invalid authentication method identifier was recieved.'));
        }

        $OAuthHelper = Builder::buildInstance($args['authentication_method']['method']);
        if (!isset($OAuthHelper) || ($OAuthHelper === false)) {
            throw new \InvalidArgumentException($this->__('The specified authentication method appears to be unsupported.'));
        }

        if (isset($args['reentrant_url']) && !empty($args['reentrant_url'])) {
            $reentrantURL = $args['reentrant_url'];
        } else {
            throw new \InvalidArgumentException($this->__('An invalid reentrant_url was recieved.'));
        }

        $checkPasswordResult = $this->internalCheckPassword($args['authentication_method'], $args['authentication_info'], $reentrantURL, false);

        if ($checkPasswordResult) {
            $passwordAuthenticates = true;

            // Set a session variable, if necessary, with the claimed id.
            if (isset($args['set_claimed_id']) && is_string($args['set_claimed_id']) && !empty($args['set_claimed_id'])) {
                $this->request->getSession()->set('claimed_id', $checkPasswordResult['claimed_id'], $args['set_claimed_id']);
            }
        }

        return $passwordAuthenticates;
    }

    /**
     * Authenticates authentication info with the authenticating source, returning simple registration information.
     *
     * ATTENTION: Any function that causes this function to be called MUST specify a return URL, and therefore
     * must be reentrant. In other words, in order to call this function, there must exist a controller function
     * (specified by the return URL) that the OAuth server can return to, and that function must restore the
     * pertinent state for the user as if he never left this site! Session variables should be used to store all
     * pertinent variables, and those must be re-read into the user's state when the return URL is called back
     * by the OAuth server.
     *
     * Note that, despite this function's name, there is no requirement that a password be part of the authentication info.
     * Merely that enough information be provided in the authentication info array to unequivocally authenticate the user. For
     * most authenticating authorities this will be the equivalent of a user name and password, but--again--there
     * is no restriction here. This is not, however, a "user exists in the system" function. It is expected that
     * the authenticating authority validate what ever is used as a password or the equivalent thereof.
     *
     * This function makes no attempt to match the given authentication info with a Zikula user id (uid). It simply asks the
     * authenticating authority to authenticate the authentication info provided. No "login" should take place as a result of
     * this authentication.
     *
     * This function may be called to initially authenticate a user during the registration process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from authenticateUser() in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid).
     *
     * This function differs from login()  in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid). In addition this function makes no attempt to
     * perform any login-related processes on the authenticating system.
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return array|boolean If the authentication info authenticates with the source, then an array is returned containing the user's 'claimed_id',
     *                              plus requested simple registration information from the OAuth server; otherwise false on authentication failure or error.
     *
     * @throws \InvalidArgumentException
     */
    public function checkPasswordForRegistration(array $args)
    {
        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new \InvalidArgumentException($this->__('An invalid set of authentication information was received.'));
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            throw new \InvalidArgumentException($this->__('An invalid authentication method identifier was recieved.'));
        }

        $OAuthHelper = Builder::buildInstance($args['authentication_method']['method']);
        if (!isset($OAuthHelper) || ($OAuthHelper === false)) {
            throw new \InvalidArgumentException($this->__('The specified authentication method appears to be unsupported.'));
        }

        if (isset($args['reentrant_url']) && !empty($args['reentrant_url'])) {
            $reentrantURL = $args['reentrant_url'];
        } else {
            throw new \InvalidArgumentException($this->__('An invalid reentrant_url was recieved.'));
        }

        $checkPasswordResult = $this->internalCheckPassword($args['authentication_method'], $args['authentication_info'], $reentrantURL, true);

        if ($checkPasswordResult) {
            $authenticationInfo = $args['authentication_info'];
            $authenticationInfo['claimed_id'] = $checkPasswordResult['claimed_id'];
            //unset($checkPasswordResult['claimed_id']);

            $checkPasswordResult = array(
                'authentication_method' => $args['authentication_method'],
                'authentication_info'   => $authenticationInfo,
                'registration_info'     => $checkPasswordResult,
            );
       }

        return $checkPasswordResult;
    }

    /**
     * Retrieves the Zikula User ID (uid) for the given authentication info
     *
     * From the mapping maintained by this authmodule.
     *
     * Custom authmodules should pay extra special attention to the accurate association of authentication info and user
     * ids (uids). Returning the wrong uid for a given authentication info will potentially expose a user's account to
     * unauthorized access. Custom authmodules must also ensure that they keep their mapping table in sync with
     * the user's account.
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * array   authentication_info The authentication information uniquely associated with a user. It should contain a 'claimed_id'.
     *
     * @param array $args All arguments passed to this function.
     *
     * @return integer|boolean The integer Zikula uid uniquely associated with the given authentication info;
     *                         otherwise false if user not found or error.
     *
     * @throws \InvalidArgumentException
     */
    public function getUidForAuthenticationInfo(array $args)
    {
        $claimedUid = false;

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new \InvalidArgumentException($this->__('An invalid set of authentication information was received.'));
        }

        if (isset($args['authentication_info']['claimed_id'])) {
            try {
                $repository = $this->entityManager->getRepository("\\Cmfcmf\\OAuthModule\\Entity\\MappedIdEntity");
                /** @var MappedIdEntity $mappedId */
                $mappedId = $repository->findOneBy(array('claimedId' => $args['authentication_info']['claimed_id']));
                if ($mappedId) {
                    $claimedUid = $mappedId->getUserId();
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                // Something went wrong.
                if (System::isDevelopmentMode()) {
                    LogUtil::registerError($e->getMessage());
                }
                return false;
            }
        }

        return $claimedUid;
    }

    /**
     * Authenticates authentication info with the authenticating source, returning the matching Zikula user id.
     *
     * This function may be called to initially authenticate a user during the login process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from checkPassword() in that the authentication info must match and be mapped to a Zikula user account,
     * and therefore must return a Zikula user id (uid). If it cannot, then it should return false, even if the authentication info
     * provided would otherwise authenticate with the authenticating authority.
     *
     * This function differs from login() in that this function makes no attempt to perform any login-related processes
     * on the authenticating system. (If there is no login-related process on the authenticating system, then this and
     * login() are functionally equivalent, however they are still logically distinct in their intent.)
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return integer|boolean If the authentication info authenticates with the source, then the Zikula uid associated with that login ID;
     *                         otherwise false on authentication failure or error.
     *
     * @throws \InvalidArgumentException
     */
    public function authenticateUser(array $args)
    {
        $authenticatedUid = false;

        if (!isset($args['authentication_info']) || empty($args['authentication_info']) || !is_array($args['authentication_info'])) {
            throw new \InvalidArgumentException($this->__('An invalid set of authentication information was received.'));
        }

        if (!isset($args['authentication_method']) || empty($args['authentication_method']) || !is_array($args['authentication_method'])) {
            throw new \InvalidArgumentException($this->__('An invalid authentication method identifier was received.'));
        }

        $passwordValidates = ModUtil::apiFunc($this->getName(), 'Authentication', 'checkPassword', array(
            'authentication_info'   => $args['authentication_info'],
            'authentication_method' => $args['authentication_method'],
            'set_claimed_id'        => 'OAuth_Authentication_authenticateUser',
            'reentrant_url'         => (isset($args['reentrant_url']) ? $args['reentrant_url'] : null),
        ));

        if ($passwordValidates) {
            $claimedID = $this->request->getSession()->get('claimed_id', false, 'OAuth_Authentication_authenticateUser');
            $this->request->getSession()->clearNamespace('OAuth_Authentication_authenticateUser');
            $args['authentication_info']['claimed_id'] = $claimedID;

            $authenticatedUid = ModUtil::apiFunc($this->getName(), 'Authentication', 'getUidForAuthenticationInfo', $args, 'Zikula_Api_AbstractAuthentication');

            if (!$authenticatedUid) {
                $this->registerError($this->__('Sorry! The information you provided was incorrect. Please check the log-in service you selected and the id you entered, and make sure they match the information associated with your account on this site.'));
                $this->registerError($this->__('If you want to create a new account, click "New account" above. If you already have an user account, please go to "My account" and associate your OAuth with that account.'));
            }
        }

        return $authenticatedUid;
    }

    /**
     * Retrieve the account recovery information for the specified user.
     *
     * The array returned by this function should be an empty array (not null) if the specified user does not have any
     * authentication methods registered with the authentication module that are enabled for log-in.
     *
     * If the specified user does have one or more authentication methods, then the array should contain one or more elements
     * indexed numerically. Each element should be an associative array containing the following:
     *
     * - 'modname' The authentication module name.
     * - 'short_description' A brief (a few words) description or name of the authentication method.
     * - 'long_description' A longer description or name of the authentication method.
     * - 'uname' The user name _equivalent_ for the authentication method (e.g., the claimed OAuth).
     * - 'link' If the authentication method is for an external service, then a link to the user's account on that service, or a general link to the service,
     *            otherwise, an empty string (not null).
     *
     * For example:
     *
     * <code>
     * $accountRecoveryInfo[] = array(
     *     'modname'           => $this->name,
     *     'short_description' => $this->__('E-mail Address'),
     *     'long_description'  => $this->__('E-mail Address'),
     *     'uname'             => $userObj['email'],
     *     'link'              => '',
     * )
     * </code>
     *
     * Parameters passed in the $arg array:
     * ------------------------------------
     * numeric 'uid' The user id of the user for which account recovery information should be retrieved.
     *
     * @param array $args All parameters passed to this function.
     *
     * @return array An array of account recovery information.
     *
     * @throws \InvalidArgumentException Thrown if the arguments array is invalid, if
     */
    public function getAccountRecoveryInfoForUid(array $args)
    {
        if (!isset($args) || empty($args)) {
            throw new \InvalidArgumentException($this->__('An invalid parameter array was received.'));
        }

        $uid = isset($args['uid']) ? $args['uid'] : false;
        if (!isset($uid) || !is_numeric($uid) || ((string)((int)$uid) != $uid)) {
            throw new \InvalidArgumentException($this->__('An invalid user id was received.'));
        }

        $userMapList = ModUtil::apiFunc($this->getName(), 'selection', 'getEntities', array('where' => array('userId' => $uid)));

        $lostUserNames = array();
        if (!empty($userMapList)) {
            foreach ($userMapList as $userMap) {
                if (isset($this->authenticationMethods[$userMap['provider']])) {
                    $authenticationMethod = $this->authenticationMethods[$userMap['provider']];
                    if ($authenticationMethod->isEnabledForAuthentication()) {
                        $lostUserNames[] = array(
                            'modname'           => $this->name,
                            'short_description' => $authenticationMethod->getShortDescription(),
                            'long_description'  => $authenticationMethod->getLongDescription(),
                            'uname'             => $userMap['claimedId'],
                            'link'              => '',
                        );
                    }
                }
            }
        }

        return $lostUserNames;
    }

    /**
     * Check whether the user shall be redirected to the registration screen if the login process fails.
     *
     * Possible reasons for the login process to fail:
     * - User does not exist yet.
     * - User provides wrong credentials.
     *
     * @param array $args {
     *     @type array $authentication_method An array identifying the selected authentication method by 'modname' and 'method'.
     *     @type array $authentication_info   An array containing the authentication information supplied by the user; for this module, a 'supplied_id'.
     * }
     *
     * @return bool True if the user shall be redirected to the registration screen, false otherwise.
     */
    public function redirectToRegistrationOnLoginError(array $args)
    {
        /** @var \Cmfcmf\OAuthModule\Helper\AuthenticationMethod $authenticationMethod */
        $authenticationMethod = $this->authenticationMethods[($args['authentication_method']['method'])];
        /** @var \Cmfcmf\OAuthModule\Provider\AbstractOAuthProvider $provider */
        $provider = $authenticationMethod->getProvider();

        return ModUtil::getVar('CmfcmfOAuthModule', 'suggestRegistrationOnFailedLogin', true) && $provider->redirectToRegistrationOnLoginError();
    }
}
