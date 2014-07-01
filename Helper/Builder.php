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

namespace Cmfcmf\OAuthModule\Helper;

/**
 * Builds concrete instances of \Cmfcmf\OAuthModule\Provider\AbstractOAuthProvider or one of its subclasses or sibling classes, based on a specified authentication method.
 */
class Builder
{
    /**
     * Builds an instance of OpenID_Helper_OpenID or one of its subclasses or sibling classes, based on a specified authentication method.
     *
     * @param string $authenticationMethod The authentication method for which a helper should be built and returned.
     *
     * @internal param array|string $authenticationInfo The authentication information entered by the user, and passed on to the helper.
     *
     * @return \Cmfcmf\OAuthModule\Provider\AbstractOAuthProvider An instance of OpenID_OpenIDProvider_AbstractProvider or one of its siblings or subclasses appropriate for the authentication
     *                                          method, and initialized with the authentication information provided.
     */
    public static function buildInstance($authenticationMethod)
    {
        $class = "\\Cmfcmf\\OAuthModule\\Provider\\" . $authenticationMethod;

        return new $class();
    }
}