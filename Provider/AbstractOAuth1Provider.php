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
 * AbstractOAuth1Provider.Class used for every OAuth 1 provider.
 */
abstract class AbstractOAuth1Provider extends AbstractOAuthProvider
{
    /**
     * This method extracts the user's password equivalent (claimed id) from the response token.
     *
     * @param StdOAuth1Token $token
     *
     * @return string The user's password equivalent (claimed id).
     */
    abstract public function extractClaimedIdFromToken(StdOAuth1Token $token);

    /**
     * {@inheritdoc}
     */
    public function getScopesMinimum()
    {
        return array();
    }
}
