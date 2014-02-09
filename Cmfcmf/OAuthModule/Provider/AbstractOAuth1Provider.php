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
 * AbstractOAuth1Provider Class used for every OAuth 1 provider.
 */
abstract class AbstractOAuth1Provider extends AbstractOAuthProvider
{
    /**
     * {@inheritdoc}
     */
    public function getScopesMinimum()
    {
        return array();
    }
}
