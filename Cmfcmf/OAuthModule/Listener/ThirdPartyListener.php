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

namespace Cmfcmf\OAuthModule\Listener;

use Cmfcmf\OAuthModule\Listener\Base\ThirdPartyListener as BaseThirdPartyListener;
use Zikula\Core\Event\GenericEvent;

/**
 * Event handler implementation class for special purposes and 3rd party api support.
 */
class ThirdPartyListener extends BaseThirdPartyListener
{
    /**
     * Listener for pending content items.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function pendingContentListener(GenericEvent $event)
    {
        parent::pendingContentListener($event);
    }
    
    /**
     * Listener for the `module.content.gettypes` event.
     *
     * This event occurs when the Content module is 'searching' for Content plugins.
     * The subject is an instance of Content_Types.
     * You can register custom content types as well as custom layout types.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function contentGetTypes(GenericEvent $event)
    {
        parent::contentGetTypes($event);
    }
    
    /**
     * Listener for the `module.scribite.editorhelpers` event.
     *
     * This occurs when Scribite adds pagevars to the editor page.
     * CmfcmfOAuthModule will use this to add a javascript helper to add custom items.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function getEditorHelpers(GenericEvent $event)
    {
        parent::getEditorHelpers($event);
    }
    
    /**
     * Listener for the `moduleplugin.tinymce.externalplugins` event.
     *
     * Adds external plugin to TinyMCE.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function getTinyMcePlugins(GenericEvent $event)
    {
        parent::getTinyMcePlugins($event);
    }
    
    /**
     * Listener for the `moduleplugin.ckeditor.externalplugins` event.
     *
     * Adds external plugin to CKEditor.
     *
     * @param GenericEvent $event The event instance.
     */
    public static function getCKEditorPlugins(GenericEvent $event)
    {
        parent::getCKEditorPlugins($event);
    }
}
