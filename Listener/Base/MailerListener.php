<?php
/**
 * OAuth.
 *
 * @copyright Christian Flach (Cmfcmf)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Christian Flach <cmfcmf.flach@gmail.com>.
 * @link https://www.github.com/cmfcmf/OAuth
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.6.1 (http://modulestudio.de).
 */

namespace Cmfcmf\OAuthModule\Listener\Base;

use Zikula\Core\Event\GenericEvent;

/**
 * Event handler base class for mailing events.
 */
class MailerListener
{
    /**
     * Listener for the `module.mailer.api.sendmessage` event.
     *
     * Invoked from `Mailer_Api_User#sendmessage`.
     * Subject is `Mailer_Api_User` with `$args`.
     * This is a notifyUntil event so the event must `$event->stopPropagation()` and set any
     * return data into `$event->data`, or `$event->setData()`.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function sendMessage(GenericEvent $event)
    {
    }
    
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return array(
            'module.mailer.api.sendmessage' => array('sendMessage', 5)
        );
    }
}