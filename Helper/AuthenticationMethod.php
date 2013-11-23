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

use ModUtil;
use Zikula\Module\UsersModule\Helper\AuthenticationMethodHelper;

class AuthenticationMethod extends AuthenticationMethodHelper
{
    public function tryToEnableForAuthentication()
    {
        $providerName =  $this->getProvider()->getProviderName();

        if (ModUtil::getVar('CmfcmfOAuthModule', 'loginProvider' . $providerName) || 1) {
            $this->enableForAuthentication();
        } else {
            $this->disableForAuthentication();
        }
    }

    public function tryToEnableForRegistration()
    {
        $providerName =  $this->getProvider()->getProviderName();

        if (ModUtil::getVar('CmfcmfOAuthModule', 'registrationProvider' . $providerName) || 1) {
            $this->enableForRegistration();
        } else {
            $this->disableForRegistration();
        }
    }

    /**
     * Get the appropriate provider class.
     *
     * @return \Cmfcmf\OAuthModule\Provider\AbstractProvider
     */
    public function getProvider()
    {
        $classname =  "\\Cmfcmf\\OAuthModule\\Provider\\{$this->getMethod()}";
        return new $classname();
    }
}