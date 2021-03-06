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

namespace Cmfcmf\OAuthModule\Controller;

use Cmfcmf\OAuthModule\Helper\Builder;
use SecurityUtil;
use Symfony\Component\Debug\Exception\FatalErrorException;
use System;
use Zikula_Controller_AbstractAuthentication;

/**
 * Provides access to administrative initiated actions for the OAuth module.
 */
class AuthenticationController extends Zikula_Controller_AbstractAuthentication
{
    /**
     * Post initialise.
     *
     * Run after construction.
     *
     * @return void
     */
    protected function postInitialize()
    {
        // Set caching to false by default.
        $this->view->setCaching(false);
    }

    /**
     * Renders the template that displays the input fields for the authentication module in the Users module's login block.
     *
     * Parameters sent in the $args array:
     * -----------------------------------
     * string $args['method']    The name of the authentication method for which the fields should be rendered.
     * string $args['form_type'] The type of form (or block, or plugin, etc.) on which the form fields will appear; used in
     *                                  computing the template name.
     *
     * @param array $args All parameters passed to this function.
     *
     * @return string The rendered template.
     *
     * @throws \InvalidArgumentException If the $args array or any parameter it contains is invalid;
     * @throws FatalErrorException If a template cannot be found for the method and the specified form type.
     */
    public function getLoginFormFieldsAction(array $args)
    {
        // Parameter extraction and error checking
        $genericErrorMessage = $this->__('An internal error has occurred while selecting a method of logging in.');
        $showDetailedErrorMessage = (System::getVar('development', false) || SecurityUtil::checkPermission($this->name . '::debug', '::', ACCESS_ADMIN));

        if (!isset($args)) {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: The $args array was empty on a call to %1$s.', array(__METHOD__));
            }
            throw new \InvalidArgumentException($errorMessage);
        } elseif (!is_array($args)) {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: The $args parameter was not an array on a call to %1$s.', array(__METHOD__));
            }
            throw new \InvalidArgumentException($errorMessage);
        }

        if (!isset($args['form_type']) || !is_string($args['form_type'])) {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: An invalid form type (\'%1$s\') was received on a call to %2$s.', array(
                    isset($args['form_type']) ? $args['form_type'] : 'NULL',
                    __METHOD__));
            }
            throw new \InvalidArgumentException($errorMessage);
        }

        if (!isset($args['method']) || !is_string($args['method']) || !$this->supportsAuthenticationMethod($args['method'])) {
            $errorMessage = $genericErrorMessage;
            if ($showDetailedErrorMessage) {
                $errorMessage .= ' ' . $this->__f('Error: An invalid method (\'%1$s\') was received on a call to %2$s.', array(
                    isset($args['form_type']) ? $args['form_type'] : 'NULL',
                    __METHOD__));
            }
            throw new \InvalidArgumentException($errorMessage);
        }
        // End parameter extraction and error checking

        if ($this->authenticationMethodIsEnabled($args['method'])) {
            $templateName = "Authentication/LoginFormFields/{$args['form_type']}/{$args['method']}.tpl";
            if (!$this->view->template_exists($templateName)) {
                $templateName = "Authentication/LoginFormFields/Default/{$args['method']}.tpl";
                if (!$this->view->template_exists($templateName)) {
                    $templateName = "Authentication/LoginFormFields/{$args['form_type']}/Default.tpl";
                    if (!$this->view->template_exists($templateName)) {
                        $templateName = "Authentication/LoginFormFields/Default/Default.tpl";
                        if (!$this->view->template_exists($templateName)) {
                            throw new FatalErrorException($this->__f('A form fields template was not found for the %1$s method using form type \'%2$s\'.', array($args['method'], $args['form_type'])));
                        }
                    }
                }
            }

            /** @var \Cmfcmf\OAuthModule\Provider\AbstractOAuthProvider $OAuthHelper */
            $OAuthHelper = Builder::buildInstance($args['method']);

            return $this->view->assign('authentication_method', $args['method'])
                  ->assign('oAuthHelper', $OAuthHelper)
                  ->fetch($templateName);
        }

        return '';
    }

    /**
     * Performs initial user-interface level validation on the authentication information received by the user from the login process.
     *
     * Each authentication method is free to define its own validation of the authentication information (user name and
     * password, or the equivalient for the authentication method), however the validation performed should be at the
     * user interface level. In other words, if all authentication information fields required by the authentication
     * method are supplied and the data is supplied in the proper form, then this validation will likely succeed, whereas
     * the actual attempt to log in with those credentials may still fail because the supplied information does not point
     * to a user. Likewise, this function may indicate that validation succeeds, but if the password (or password equivalent)
     * does not match that on file for the user to whom the credentials resolve then the attempt to log in with those
     * credentials may still fail.
     *
     * If this function returns true, indicating that validation is successful, then it *must be possible* (although not
     * guaranteed) to successfully log in with the validated credentials. If this function returns false, indicating that
     * validation was not successful, then it *must be impossible* to use the supplied credentials to log in under any
     * circumstances at all. When this function returns false, it must also set the appropriate error message for the
     * user's redirection to an appropriate page by the calling function (or it must ensure that one has been set by some
     * subordinate function).
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * - array $args['authenticationMethod'] The authentication method (selected either by the user or by the system) for which
     *                                          the credentials in $authenticationInfo were entered by the user. This array will
     *                                          contain 'modname', the name of the module that defines the authentication method,
     *                                          and 'method', the name of the specific method being used.
     * - array $args['authenticationInfo']   The user's credentials, as supplied by him on a log-in form on the log-in screen,
     *                                          log-in block, or some other equivalent control. The contents of the array are
     *                                          specified by the specific authentication method, but typically contains an
     *                                          equivalent to a user name, and possibly an equivalent to a password (especially
     *                                          if the authentication method does not perform external third-party authentication
     *                                          via a federated authentication service).
     *
     * @param array $args The parameters for this function.
     *
     * @return boolean True if the authentication information (the user's credentials) pass initial user-interface level validation;
     *                  otherwise false and an error status message is set.
     *
     * @throws \InvalidArgumentException If $args are not valid.
     */
    public function validateAuthenticationInformationAction(array $args)
    {
        if (!isset($args['authenticationMethod']) || !isset($args['authenticationInfo'])) {
            throw new \InvalidArgumentException($this->__('Error: An invalid \'$args\' parameter was received.'));
        }

        if (empty($args['authenticationInfo']['supplied_id'])) {
            return false;
        }

        return true;
    }
}
