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

use ModUtil;
use ServiceUtil;
use Zikula\Core\Event\GenericEvent;

/**
 * Event handler base class for user-related events.
 */
class UserListener
{
    /**
     * Listener for the `user.gettheme` event.
     *
     * Called during UserUtil::getTheme() and is used to filter the results.
     * Receives arg['type'] with the type of result to be filtered
     * and the $themeName in the $event->data which can be modified.
     * Must $event->stopPropagation() if handler performs filter.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function getTheme(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `user.account.create` event.
     *
     * Occurs after a user account is created. All handlers are notified.
     * It does not apply to creation of a pending registration.
     * The full user record created is available as the subject.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     * The subject of the event is set to the user record that was created.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function create(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `user.account.update` event.
     *
     * Occurs after a user is updated. All handlers are notified.
     * The full updated user record is available as the subject.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     * The subject of the event is set to the user record, with the updated values.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function update(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `user.account.delete` event.
     *
     * Occurs after a user is deleted from the system.
     * All handlers are notified.
     * The full user record deleted is available as the subject.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     * The subject of the event is set to the user record that is being deleted.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function delete(GenericEvent $event)
    {
        ModUtil::initOOModule('CmfcmfOAuthModule');
    
        $userRecord = $event->getSubject();
        $uid = $userRecord['uid'];
        $serviceManager = ServiceUtil::getManager();
        $entityManager = $serviceManager->getService('doctrine.entitymanager');
        
        $repo = $entityManager->getRepository('Cmfcmf\OAuthModule\Entity\UserEntity');
        // delete all users created by this user
        $repo->deleteCreator($uid);
        // note you could also do: $repo->updateCreator($uid, 2);
        
        // set last editor to admin (2) for all users updated by this user
        $repo->updateLastEditor($uid, 2);
        // note you could also do: $repo->deleteLastEditor($uid);
        // set user id to guest (1) for all affected users
        $repo->updateUserField('userId', $uid, 1);
    }
}
