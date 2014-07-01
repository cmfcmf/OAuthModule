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
    public function getApplicationRegistrationDoc()
    {
        $message = $this->__('You need to register an application at <a href="https://cloud.google.com/console/project">https://cloud.google.com/console/project</a> to use Google OAuth.');
        return $message . '<br />' . parent::getApplicationRegistrationDoc();
    }
}