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
     */
    public function getApplicationRegistrationDoc()
    {
        $message = $this->__('You need to register an application at <a href="https://dev.twitter.com/apps">https://dev.twitter.com/apps</a> to use Twitter OAuth.');
        return $message . '<br />' . parent::getApplicationRegistrationDoc();
    }
}
