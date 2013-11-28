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
 * AbstractOAuth2Provider.Class used for every OAuth 2 provider.
 */
abstract class AbstractOAuth2Provider extends AbstractOAuthProvider
{
    /**
     * {@inheritdoc}
     * @param TokenInterface $token
     *
     * @return string
     */
    public function extractClaimedIdFromToken(TokenInterface $token)
    {
        return $token->getAccessToken();
    }
}
