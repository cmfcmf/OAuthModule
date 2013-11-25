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
        return array('https://www.googleapis.com/auth/userinfo.email');
    }

    /**
     * {@inheritdoc}
     */
    public function getScopesMaximum()
    {
        return array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile');
    }

    /**
     * {@inheritdoc}
     *
     * @param \OAuth\OAuth2\Service\Google  $google
     *
     * @return array
     *
     * @todo To be implemented!!
     */
    public function getAdditionalInformationForRegistration($google)
    {
        try {
            return array();
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