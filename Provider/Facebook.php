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

use OAuth\OAuth2\Service\Facebook as FacebookService;

/**
 * The Facebook OAuth 2 provider class.
 */
class Facebook extends AbstractOAuth2Provider
{
    /**
     * {@inheritdoc}
     */
    public function getProviderDisplayName()
    {
        return $this->__('Facebook');
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'fa-facebook';
    }

    /**
     * {@inheritdoc}
     */
    public function getScopesForLogin()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     *
     * @see FacebookService::SCOPE_EMAIL
     *
     * Only use email scope.
     */
    public function getScopesForRegistration()
    {
        return array(FacebookService::SCOPE_EMAIL);
    }

    /**
     * {@inheritdoc}
     *
     * @param \OAuth\OAuth2\Service\Facebook  $facebook
     *
     * @return array
     *
     * @todo To be implemented!
     */
    public function getAdditionalInformationForRegistration($facebook)
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
     *
     * @todo To be implemented!
     */
    public function getApplicationRegistrationDoc()
    {
        parent::getApplicationRegistrationDoc();
        //$message = $this->__('You need to register an application at <a href="https://github.com/settings/applications/new">https://github.com/settings/applications/new</a> to use GitHub OAuth.');
        //return $message . '<br />' . parent::getApplicationRegistrationDoc();
    }
}