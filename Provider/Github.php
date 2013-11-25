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

use OAuth\OAuth2\Service\GitHub as GitHubService;

/**
 * The Github OAuth.2 provider class.
 */
class Github extends AbstractOAuth2Provider
{
    /**
     * {@inheritdoc}
     */
    public function getOAuthServiceName()
    {
        return 'GitHub';
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderDisplayName()
    {
        return $this->__('GitHub');
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'fa-github';
    }

    /**
     * {@inheritdoc}
     */
    public function getScopesMinimum()
    {
        return array(GitHubService::SCOPE_READONLY);
    }

    /**
     * {@inheritdoc}
     */
    public function getScopesMaximum()
    {
        return array(GitHubService::SCOPE_USER_EMAIL);
    }

    /**
     * {@inheritdoc}
     *
     * @param \OAuth\OAuth2\Service\GitHub  $github
     *
     * @return array
     *
     * Fetches the user's email address and GitHub user name.
     */
    public function getAdditionalInformationForRegistration($github)
    {
        try {
            $result = json_decode($github->request('user/emails'), true);
            $email = $result[0];

            $result = json_decode($github->request('user'), true);
            $uname = $result['login'];

            return array('email' => $email, 'hideEmail' => true, 'uname' => $uname);
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
        $message = $this->__('You need to register an application at <a href="https://github.com/settings/applications/new">https://github.com/settings/applications/new</a> to use GitHub OAuth.');
        return $message . '<br />' . parent::getApplicationRegistrationDoc();
    }
}