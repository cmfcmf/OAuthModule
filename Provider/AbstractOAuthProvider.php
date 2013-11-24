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

namespace Cmfcmf\OAuthModule\Provider;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\Common\Storage\SymfonySession;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\ServiceFactory;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Zikula_TranslatableInterface;
use ZLanguage;

/**
 * AbstractOAuthProvider.Class used for every OAuth provider.
 */
abstract class AbstractOAuthProvider implements Zikula_TranslatableInterface
{
    /**
     * @var string The language domain.
     */
    protected $domain;

    /**
     * Builds a new instance of this class, and sets the translation domain.
     */
    public final function __construct()
    {
        $this->domain = ZLanguage::getModuleDomain('CmfcmfOAuthModule');
    }

    /**
     * Return an array of scopes needed for login.
     *
     * @return array The array of scopes.
     */
    abstract public function getScopesForLogin();

    /**
     * Return an array of scopes for registration.
     *
     * @return array The array of scopes.
     */
    abstract function getScopesForRegistration();

    /**
     * Returns the name of a FontAwesome icon or a path to an image to be used as the icon for this provider.
     *
     * @return string The icon.
     */
    abstract function getIcon();

    /**
     * Returns if the provider is registration capable.
     *
     * @return bool True if the provider is registration capable.
     */
    public function registrationCapable()
    {
        return true;
    }

    /**
     * Determines if the user shall be redirected to the registration screen if login isn't successful.
     *
     * @return bool
     *
     * It defaults to the content of the module variable $suggestRegistrationOnFailedLogin and $this->registrationCapable().
     */
    public function redirectToRegistrationOnLoginError()
    {
        return \ModUtil::getVar('CmfcmfOAuthModule', 'suggestRegistrationOnFailedLogin', true) && $this->registrationCapable();
    }

    /**
     * Returns the information which shall be shown above the provider configuration.
     *
     * @return string The information.
     *
     * Defaults to a general message to insert consumer key and secret below.
     */
    public function getApplicationRegistrationDoc()
    {
        return $this->__('After creating your application, please insert the "Consumer key" and the "Consumer secret" below.');
    }

    /**
     * Get further information about the user from $service.
     *
     * @param \OAuth\Common\Service\AbstractService $service
     *
     * @return array Further information extracted from service.
     *
     * Possible extracted information:
     * - 'uname': The user name.
     * - 'email': The email address.
     * - 'hideEmail': Whether to hide the email address fields during registration. This does NOT mean the email cannot
     * be changed. It will be hidden only and still can be changed by the user. Maybe it even must be changed, if the
     * email address is in use already.
     * - 'lang': The user's preferred language.
     */
    public function getAdditionalInformationForRegistration($service)
    {
        unset($service);
        return array();
    }

    /**
     * Get the service name of the provider used by the OAuth library. Defaults to the class name.
     *
     * @return string The service provider name.
     */
    public function getOAuthServiceName()
    {
        return $this->getProviderName();
    }

    /**
     * Returns the provider's display name. Defaults to the class name.
     *
     * @return string The provider's display name.
     */
    public function getProviderDisplayName()
    {
        return $this->getProviderName();
    }

    /**
     * Returns the provider's short description. Defaults to the provider's display name.
     *
     * @return string The provider's short description.
     */
    public function getShortDescription()
    {
        return $this->getProviderDisplayName();
    }

    /**
     * Returns the provider's long description. Defaults to the provider's short description + "Account".
     *
     * @return string The provider's long description.
     */
    public function getLongDescription()
    {
        return $this->__f('%s Account', array($this->getShortDescription()));
    }

    /**
     * Creates a new service using the given $strorage, $scopes and $reentrantURL.
     *
     * @param TokenStorageInterface $storage      The storage to be used.
     * @param array                 $scopes       The scopes to request.
     * @param string                $reentrantURL The reentrant url to use.
     *
     * @return \OAuth\Common\Service\ServiceInterface
     * @throws \Symfony\Component\Debug\Exception\FatalErrorException If no credentials are available.
     */
    public function createService(TokenStorageInterface $storage, array $scopes = array(), $reentrantURL)
    {
        /** @var $serviceFactory ServiceFactory An OAuth service factory. */
        $serviceFactory = new ServiceFactory();

        $credentials = $this->getCredentials();

        if (!isset($credentials['key']) || !isset($credentials['secret'])) {
            throw new FatalErrorException($this->__f('Invalid credentials for the %s OAuth provider received.', $this->getOAuthServiceName()));
        }

        // Setup the credentials for the requests
        $credentials = new Credentials(
            $credentials['key'],
            $credentials['secret'],
            $reentrantURL
        );

        return $serviceFactory->createService($this->getOAuthServiceName(), $credentials, $storage, $scopes);
    }

    /**
     * Returns an array with the given credentials for the provider.
     *
     * @return array An array of key and secret with the credentials read out of module variables.
     */
    public final function getCredentials()
    {
        $key = \ModUtil::getVar('CmfcmfOAuthModule', 'key' . $this->getProviderName());
        $secret = \ModUtil::getVar('CmfcmfOAuthModule', 'secret' . $this->getProviderName());
        return array('key' => $key, 'secret' => $secret);
    }

    /**
     * Calculate the provider name out of the class name.
     *
     * @return string The provider name.
     */
    public final function getProviderName()
    {
        $parts = explode('\\', get_class($this));

        return $parts[count($parts) - 1];
    }

    /**
     * Determines whether the icon is a FontAwesome icon or the path to an image.
     *
     * @return bool True if it is a FontAwesome icon, false otherwise.
     */
    public final function isFontAwesomeIcon()
    {
        $icon = $this->getIcon();
        if(strpos($icon, '/') !== false || strpos($icon, 'fa-') !== 0 || empty($icon)) {
            return false;
        }
        return true;
    }

    /**
     * Translate.
     *
     * @param string $msgid String to be translated.
     *
     * @return string
     */
    public final function __($msgid)
    {
        return __($msgid, $this->domain);
    }

    /**
     * Translate with sprintf().
     *
     * @param string       $msgid  String to be translated.
     * @param string|array $params Args for sprintf().
     *
     * @return string
     */
    public final function __f($msgid, $params)
    {
        return __f($msgid, $params, $this->domain);
    }

    /**
     * Translate plural string.
     *
     * @param string $singular Singular instance.
     * @param string $plural   Plural instance.
     * @param string $count    Object count.
     *
     * @return string Translated string.
     */
    public final function _n($singular, $plural, $count)
    {
        return _n($singular, $plural, $count, $this->domain);
    }

    /**
     * Translate plural string with sprintf().
     *
     * @param string       $sin    Singular instance.
     * @param string       $plu    Plural instance.
     * @param string       $n      Object count.
     * @param string|array $params Sprintf() arguments.
     *
     * @return string
     */
    public final function _fn($sin, $plu, $n, $params)
    {
        return _fn($sin, $plu, $n, $params, $this->domain);
    }
}
