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

use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Http\Client\StreamClient;
use OAuth\Common\Token\TokenInterface;
use OAuth\ServiceFactory;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use Symfony\Component\Debug\Exception\FatalErrorException;
use OAuth\OAuth2\Service\Google as GoogleService;

/**
 * The Google OAuth.2 provider class.
 */
class Google extends AbstractOAuth2Provider
{
    /**
     * {@inheritdoc}
     */
    public function getProviderDisplayName()
    {
        return $this->__('Google');
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'fa-google-plus';
    }

    /**
     * {@inheritdoc}
     */
    public function getScopesMinimum()
    {
        return array(GoogleService::SCOPE_USERINFO_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function getScopesMaximum()
    {
        return array(GoogleService::SCOPE_USERINFO_EMAIL, GoogleService::SCOPE_USERINFO_PROFILE);
    }

    /**
     * {@inheritdoc}
     */
    public function extractClaimedIdFromToken(TokenInterface $token)
    {
        unset($token);
        $result = json_decode($this->service->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

        return $result['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInformationForRegistration()
    {
        try {
            $result = json_decode($this->service->request('https://www.googleapis.com/oauth2/v3/userinfo'), true);

            return array(
                'uname' => $this->sanitizeUname($result['name']),
                'email' => $result['email'],
                'hideEmail' => true,
                'emailVerified' => $result['email_verified'] === "true",
                'lang' => $result['locale']
            );
        } catch (\Exception $e) {
            // Catch anything.
            return array();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicationRegistrationDoc()
    {
        $message = $this->__('You need to register an application at <a href="https://cloud.google.com/console#/project">https://cloud.google.com/console#/project</a> to use Google OAuth.');
        return $message . '<br />' . parent::getApplicationRegistrationDoc();
    }
}