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

namespace Cmfcmf\OAuthModule\Util;

use Symfony\Component\Finder\Finder;


/**
 * Class ProviderUtil.
 */
class ProviderUtil
{
    /**
     * Returns all the available OAuth provider.
     *
     * @return \Cmfcmf\OAuthModule\Provider\AbstractOAuthProvider[]
     */
    public static function getAllOAuthProvider()
    {
        $finder = new Finder();
        $finder->files()
            ->in(dirname(__FILE__) . "/../Provider")
            ->name('*.php')
            ->notName('AbstractOAuthProvider.php')
            ->notName('AbstractOAuth1Provider.php')
            ->notName('AbstractOAuth2Provider.php')
            ->depth('== 0')
            ->sortByName();

        $provider = array();

        foreach ($finder as $file) {
            $classname =  '\Cmfcmf\OAuthModule\Provider\\' . substr($file->getRelativePathname(), 0, -4);
            $provider[] = new $classname();
        }

        return $provider;
    }
}