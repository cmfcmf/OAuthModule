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

use OAuth\Common\Token\TokenInterface;

/**
 * The Twitter OAuth 2 provider class.
 */
class Twitter extends AbstractOAuth1Provider
{
    /**
     * {@inheritdoc}
     */
    public function getProviderDisplayName()
    {
        return $this->__('Twitter');
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'fa-twitter';
    }

    /**
     * {@inheritdoc}
     * @param TokenInterface $token
     *
     * @return string
     */
    public function extractClaimedIdFromToken(TokenInterface $token)
    {
        $params = $token->getExtraParams();

        return $params['user_id'];
    }

    /**
     * {@inheritdoc}
     *
     * NOTE: Twitter does NOT provide the user's email address.
     */
    public function getAdditionalInformationForRegistration()
    {
        try {
            $result = json_decode($this->service->request('account/settings.json'), true);
            $uname = $result['screen_name'];
            $lang = $result['language'];

            return array('lang' => $lang, 'uname' => $uname);
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
        $message = $this->__('You need to register an application at <a href="https://dev.twitter.com/apps">https://dev.twitter.com/apps</a> to use Twitter OAuth.');
        return $message . '<br />' . parent::getApplicationRegistrationDoc();
    }
}
