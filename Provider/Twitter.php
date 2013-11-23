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

use OAuth\OAuth1\Token\StdOAuth1Token;

/**
 * A helper or utility class that provides information for a Twitter Account OAuth in expected formats for the protocol.
 */
class Twitter extends AbstractProvider
{
    public function getProviderDisplayName()
    {
        return $this->__('Twitter');
    }

    public function getIcon()
    {
        return 'fa-twitter';
    }

    public function getScopesForLogin()
    {
        return array();
    }

    public function getScopesForRegistration()
    {
        return array();
    }

    public function extractClaimedIdFromToken(StdOAuth1Token $token)
    {
        $params = $token->getExtraParams();

        return $params['user_id'];
    }

    /**
     * @inheritdoc
     *
     * @param \OAuth\OAuth1\Service\Twitter  $twitter
     *
     * @return array
     *
     * @note Twitter does NOT provide the user's email address.
     */
    public function getAdditionalInformationForRegistration($twitter)
    {
        try {
            $result = json_decode($twitter->request('account/settings.json'), true);
            $uname = $result['screen_name'];
            $lang = $result['language'];

            return array('lang' => $lang, 'uname' => $uname);
        } catch (\Exception $e) {
            // Catch anything.
            return array();
        }
    }
}