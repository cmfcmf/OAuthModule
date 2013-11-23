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
 * A helper or utility class that provides information for a Github Account OAuth in expected formats for the protocol.
 */
class Github extends AbstractProvider
{
    public function getOAuthServiceName()
    {
        return 'GitHub';
    }

    public function getProviderDisplayName()
    {
        return $this->__('GitHub');
    }

    public function getIcon()
    {
        return 'fa-github';
    }

    public function getScopesForLogin()
    {
        return array();
    }

    public function getScopesForRegistration()
    {
        return array('user:email');
    }

    /**
     * @inheritdoc
     *
     * @param \OAuth\OAuth2\Service\GitHub  $github
     *
     * @return array
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
}