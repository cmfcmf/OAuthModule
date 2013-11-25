<?php
/**
 * OAuth.
 *
 * @copyright Christian Flach (Cmfcmf)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Christian Flach <cmfcmf.flach@gmail.com>.
 * @link https://www.github.com/cmfcmf/OAuth
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.6.1 (http://modulestudio.de) at Mon Nov 25 19:51:13 CET 2013.
 */

namespace Cmfcmf\OAuthModule\Base;

use HookUtil;
use Zikula_AbstractVersion;
use Zikula\Component\HookDispatcher\ProviderBundle;
use Zikula\Component\HookDispatcher\SubscriberBundle;

/**
 * Version information base class.
 */
class OAuthModuleVersion extends Zikula_AbstractVersion
{
    /**
     * Retrieves meta data information for this application.
     *
     * @return array List of meta data.
     */
    public function getMetaData()
    {
        $meta = array();
        // the current module version
        $meta['version']              = '1.0.0';
        // the displayed name of the module
        $meta['displayname']          = $this->__('O auth');
        // the module description
        $meta['description']          = $this->__('O auth module generated by ModuleStudio 0.6.1.');
        //! url version of name, should be in lowercase without space
        $meta['url']                  = $this->__('oauth');
        // core requirement
        $meta['core_min']             = '1.3.7'; // requires minimum 1.3.7 or later
        $meta['core_max']             = '1.3.99'; // not ready for 1.4.0 yet

        // define special capabilities of this module
        $meta['capabilities'] = array(
                          HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true)
/*,
                          HookUtil::PROVIDER_CAPABLE => array('enabled' => true), // TODO: see #15
                          'authentication' => array('version' => '1.0'),
                          'profile'        => array('version' => '1.0', 'anotherkey' => 'anothervalue'),
                          'message'        => array('version' => '1.0', 'anotherkey' => 'anothervalue')
*/
        );

        // permission schema
        $meta['securityschema'] = array(
            'CmfcmfOAuthModule::' => '::',
            'CmfcmfOAuthModule::Ajax' => '::',
            'CmfcmfOAuthModule:ItemListBlock:' => 'Block title::',
            'CmfcmfOAuthModule:MappedId:' => 'MappedId ID::',
        );
        // DEBUG: permission schema aspect ends


        return $meta;
    }

    /**
     * Define hook subscriber bundles.
     */
    protected function setupHookBundles()
    {
        
        $bundle = new SubscriberBundle($this->name, 'subscriber.cmfcmfoauthmodule.ui_hooks.mappedids', 'ui_hooks', __('cmfcmfoauthmodule Mapped ids Display Hooks'));
        
        // Display hook for view/display templates.
        $bundle->addEvent('display_view', 'cmfcmfoauthmodule.ui_hooks.mappedids.display_view');
        // Display hook for create/edit forms.
        $bundle->addEvent('form_edit', 'cmfcmfoauthmodule.ui_hooks.mappedids.form_edit');
        // Display hook for delete dialogues.
        $bundle->addEvent('form_delete', 'cmfcmfoauthmodule.ui_hooks.mappedids.form_delete');
        // Validate input from an ui create/edit form.
        $bundle->addEvent('validate_edit', 'cmfcmfoauthmodule.ui_hooks.mappedids.validate_edit');
        // Validate input from an ui create/edit form (generally not used).
        $bundle->addEvent('validate_delete', 'cmfcmfoauthmodule.ui_hooks.mappedids.validate_delete');
        // Perform the final update actions for a ui create/edit form.
        $bundle->addEvent('process_edit', 'cmfcmfoauthmodule.ui_hooks.mappedids.process_edit');
        // Perform the final delete actions for a ui form.
        $bundle->addEvent('process_delete', 'cmfcmfoauthmodule.ui_hooks.mappedids.process_delete');
        $this->registerHookSubscriberBundle($bundle);

        $bundle = new SubscriberBundle($this->name, 'subscriber.cmfcmfoauthmodule.filter_hooks.mappedids', 'filter_hooks', __('cmfcmfoauthmodule Mapped ids Filter Hooks'));
        // A filter applied to the given area.
        $bundle->addEvent('filter', 'cmfcmfoauthmodule.filter_hooks.mappedids.filter');
        $this->registerHookSubscriberBundle($bundle);

        
    }
}
