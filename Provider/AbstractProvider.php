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

abstract class AbstractProvider implements Zikula_TranslatableInterface
{

    protected $domain;

    /**
     * Builds a new instance of this class, extracting the supplied OpenID from the $authenticationInfo parameter.
     */
    public final function __construct()
    {
        $this->domain = ZLanguage::getModuleDomain('CmfcmfOAuthModule');
    }

    abstract public function getScopesForLogin();

    abstract public function getScopesForRegistration();

    /**
     * Builds a service.
     *
     * @param array $scopes
     * @param       $reentrantURL
     *
     * @return \OAuth\Common\Service\ServiceInterface
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

    public function getOAuthServiceName()
    {
        return $this->getProviderName();
    }

    public function getCredentials()
    {
        $key = \ModUtil::getVar('CmfcmfOAuthModule', lcfirst($this->getProviderName()) . 'ConsumerKey');
        $secret = \ModUtil::getVar('CmfcmfOAuthModule', lcfirst($this->getProviderName()) . 'ConsumerSecret');
        return array('key' => $key, 'secret' => $secret);
    }

    public final function getProviderName()
    {
        $parts = explode('\\', get_class($this));

        return $parts[count($parts) - 1];
    }

    public function getProviderDisplayName()
    {
        return $this->getProviderName();
    }

    public function getShortDescription()
    {
        return $this->getProviderDisplayName();
    }

    public function getLongDescription()
    {
        return $this->__f('%s Account', array($this->getShortDescription()));
    }

    public function registrationCapable()
    {
        return true;
    }

    public function getIcon()
    {
        return false;
    }

    public function isFontAwesomeIcon()
    {
        $icon = $this->getIcon();
        if(strpos($icon, '/') !== false || strpos($icon, 'fa-') !== 0 || empty($icon)) {
            return false;
        }
        return true;
    }

    public function redirectToRegistrationOnLoginError()
    {
        return true;
    }

    public function getAdditionalInformationFromTokenForRegistration($service)
    {
        unset($service);
        return array();
    }

    public function extractClaimedIdFromToken(StdOAuth1Token $token)
    {
        throw new \LogicException('You must override this method in your concrete class.');
    }

    /**
     * Translate.
     *
     * @param string $msgid String to be translated.
     *
     * @return string
     */
    public function __($msgid)
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
    public function __f($msgid, $params)
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
    public function _n($singular, $plural, $count)
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
    public function _fn($sin, $plu, $n, $params)
    {
        return _fn($sin, $plu, $n, $params, $this->domain);
    }
}
