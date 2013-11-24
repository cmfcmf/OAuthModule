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

namespace Cmfcmf\OAuthModule\Base;

use Cmfcmf\OAuthModule\Util\ControllerUtil;

use DoctrineHelper;
use EventUtil;
use HookUtil;
use LogUtil;
use ModUtil;
use System;
use Zikula_AbstractInstaller;
use Zikula_Workflow_Util;

/**
 * Installer base class.
 */
class OAuthModuleInstaller extends Zikula_AbstractInstaller
{
    /**
     * Install the CmfcmfOAuthModule application.
     *
     * @return boolean True on success, or false.
     *
     * @throws RuntimeException Thrown if database tables can not be created or another error occurs
     */
    public function install()
    {
        // create all tables from according entity definitions
        try {
            DoctrineHelper::createSchema($this->entityManager, $this->listEntityClasses());
        } catch (\Exception $e) {
            if (System::isDevelopmentMode()) {
                throw new \RuntimeException($this->__('Doctrine Exception: ') . $e->getMessage());
            }
            $returnMessage = $this->__f('An error was encountered while creating the tables for the %s extension.', array($this->name));
            if (!System::isDevelopmentMode()) {
                $returnMessage .= ' ' . $this->__('Please enable the development mode by editing the /config/config.php file in order to reveal the error details.');
            }
            throw new \RuntimeException($returnMessage);
        }
    
        // set up all our vars with initial values
        $this->setVar('twitterConsumerKey', '');
        $this->setVar('twitterConsumerSecret', '');
        $this->setVar('loginProviderTwitter', false);
        $this->setVar('loginProviderGithub', false);
        $this->setVar('loginProviderGoogle', false);
        $this->setVar('registrationProviderTwitter', false);
        $this->setVar('registrationProviderGithub', false);
        $this->setVar('registrationProviderGoogle', false);
        $this->setVar('githubConsumerKey', '');
        $this->setVar('githubConsumerSecret', '');
        $this->setVar('googleConsumerKey', '');
        $this->setVar('googleConsumerSecret', '');
    
        $categoryRegistryIdsPerEntity = array();
    
        // create the default data
        $this->createDefaultData($categoryRegistryIdsPerEntity);
    
        // register persistent event handlers
        $this->registerPersistentEventHandlers();
    
        // register hook subscriber bundles
        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());
        
    
        // initialisation successful
        return true;
    }
    
    /**
     * Upgrade the CmfcmfOAuthModule application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldVersion Version to upgrade from.
     *
     * @return boolean True on success, false otherwise.
     *
     * @throws RuntimeException Thrown if database tables can not be updated
     */
    public function upgrade($oldVersion)
    {
    /*
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case 1.0.0:
                // do something
                // ...
                // update the database schema
                try {
                    DoctrineHelper::updateSchema($this->entityManager, $this->listEntityClasses());
                } catch (\Exception $e) {
                    if (System::isDevelopmentMode()) {
                        throw new \RuntimeException($this->__('Doctrine Exception: ') . $e->getMessage());
                    }
                    throw new \RuntimeException($this->__f('An error was encountered while updating tables for the %s extension.', array($this->getName())));
                }
        }
    
        // Note there are several helpers available for making migration of your extension easier.
        // The following convenience methods are each responsible for a single aspect of upgrading to Zikula 1.3.6.
    
        // here is a possible usage example
        // of course 1.2.3 should match the number you used for the last stable 1.3.5 module version.
        /* if ($oldVersion = 1.2.3) {
            // rename module for all modvars
            $this->updateModVarsTo136();
            
            // update extension information about this app
            $this->updateExtensionInfoFor136();
            
            // rename existing permission rules
            $this->renamePermissionsFor136();
            
            // rename existing category registries
            $this->renameCategoryRegistriesFor136();
            
            // rename all tables
            $this->renameTablesFor136();
            
            // drop handlers for obsolete events
            $this->unregisterEventHandlersObsoleteIn136();
            
            // register two new event handlers
            $this->registerNewEventHandlersIn136();
            
            // update module name in the hook tables
            $this->updateHookNamesFor136();
        } * /
    */
    
        // update successful
        return true;
    }
    
    /**
     * Returns the name of the default system database.
     *
     * @return string the database name.
     */
    protected function getDbName()
    {
        return $this->getContainer()->getParameter('database_name');
    }
    
    
    /**
     * Uninstall CmfcmfOAuthModule.
     *
     * @return boolean True on success, false otherwise.
     *
     * @throws RuntimeException Thrown if database tables or stored workflows can not be removed
     */
    public function uninstall()
    {
        // delete stored object workflows
        $result = Zikula_Workflow_Util::deleteWorkflowsForModule($this->getName());
        if ($result === false) {
            throw new \RuntimeException($this->__f('An error was encountered while removing stored object workflows for the %s extension.', array($this->getName())));
        }
    
        try {
            DoctrineHelper::dropSchema($this->entityManager, $this->listEntityClasses());
        } catch (\Exception $e) {
            if (System::isDevelopmentMode()) {
                throw new \RuntimeException($this->__('Doctrine Exception: ') . $e->getMessage());
            }
            throw new \RuntimeException($this->__f('An error was encountered while dropping tables for the %s extension.', array($this->name)));
        }
    
        // unregister persistent event handlers
        EventUtil::unregisterPersistentModuleHandlers($this->name);
    
        // unregister hook subscriber bundles
        HookUtil::unregisterSubscriberBundles($this->version->getHookSubscriberBundles());
        
    
        // remove all module vars
        $this->delVars();
    
        // uninstallation successful
        return true;
    }
    
    /**
     * Build array with all entity classes for CmfcmfOAuthModule.
     *
     * @return array list of class names.
     */
    protected function listEntityClasses()
    {
        $classNames = array();
        $classNames[] = 'Cmfcmf\OAuthModule\Entity\UserEntity';
    
        return $classNames;
    }
    
    /**
     * Create the default data for CmfcmfOAuthModule.
     *
     * @param array $categoryRegistryIdsPerEntity List of category registry ids.
     *
     * @return void
     */
    protected function createDefaultData($categoryRegistryIdsPerEntity)
    {
        $entityClass = '\\Cmfcmf\\OAuthModule\\Entity\\UserEntity';
        $this->entityManager->getRepository($entityClass)->truncateTable();
    }
    
    /**
     * Register persistent event handlers.
     * These are listeners for external events of the core and other modules.
     */
    protected function registerPersistentEventHandlers()
    {
        // core -> 
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'api.method_not_found', array('Cmfcmf\OAuthModule\Listener\CoreListener', 'apiMethodNotFound'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'core.preinit', array('Cmfcmf\OAuthModule\Listener\CoreListener', 'preInit'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'core.init', array('Cmfcmf\OAuthModule\Listener\CoreListener', 'init'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'core.postinit', array('Cmfcmf\OAuthModule\Listener\CoreListener', 'postInit'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'controller.method_not_found', array('Cmfcmf\OAuthModule\Listener\CoreListener', 'controllerMethodNotFound'));
    
        // front controller -> Cmfcmf\OAuthModule\Listener\FrontControllerListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'frontcontroller.predispatch', array('Cmfcmf\OAuthModule\Listener\FrontControllerListener', 'preDispatch'));
    
        // installer -> Cmfcmf\OAuthModule\Listener\InstallerListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'installer.module.installed', array('Cmfcmf\OAuthModule\Listener\InstallerListener', 'moduleInstalled'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'installer.module.upgraded', array('Cmfcmf\OAuthModule\Listener\InstallerListener', 'moduleUpgraded'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'installer.module.uninstalled', array('Cmfcmf\OAuthModule\Listener\InstallerListener', 'moduleUninstalled'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'installer.module.activated', array('Cmfcmf\OAuthModule\Listener\InstallerListener', 'moduleActivated'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'installer.module.deactivated', array('Cmfcmf\OAuthModule\Listener\InstallerListener', 'moduleDeactivated'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'installer.subscriberarea.uninstalled', array('Cmfcmf\OAuthModule\Listener\InstallerListener', 'subscriberAreaUninstalled'));
    
        // modules -> Cmfcmf\OAuthModule\Listener\ModuleDispatchListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module_dispatch.postloadgeneric', array('Cmfcmf\OAuthModule\Listener\ModuleDispatchListener', 'postLoadGeneric'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module_dispatch.preexecute', array('Cmfcmf\OAuthModule\Listener\ModuleDispatchListener', 'preExecute'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module_dispatch.postexecute', array('Cmfcmf\OAuthModule\Listener\ModuleDispatchListener', 'postExecute'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module_dispatch.custom_classname', array('Cmfcmf\OAuthModule\Listener\ModuleDispatchListener', 'customClassname'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module_dispatch.service_links', array('Cmfcmf\OAuthModule\Listener\ModuleDispatchListener', 'serviceLinks'));
    
        // mailer -> Cmfcmf\OAuthModule\Listener\MailerListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.mailer.api.sendmessage', array('Cmfcmf\OAuthModule\Listener\MailerListener', 'sendMessage'));
    
        // page -> Cmfcmf\OAuthModule\Listener\PageListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'pageutil.addvar_filter', array('Cmfcmf\OAuthModule\Listener\PageListener', 'pageutilAddvarFilter'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'system.outputfilter', array('Cmfcmf\OAuthModule\Listener\PageListener', 'systemOutputfilter'));
    
        // theme -> Cmfcmf\OAuthModule\Listener\ThemeListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'theme.preinit', array('Cmfcmf\OAuthModule\Listener\ThemeListener', 'preInit'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'theme.init', array('Cmfcmf\OAuthModule\Listener\ThemeListener', 'init'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'theme.load_config', array('Cmfcmf\OAuthModule\Listener\ThemeListener', 'loadConfig'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'theme.prefetch', array('Cmfcmf\OAuthModule\Listener\ThemeListener', 'preFetch'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'theme.postfetch', array('Cmfcmf\OAuthModule\Listener\ThemeListener', 'postFetch'));
    
        // view -> Cmfcmf\OAuthModule\Listener\ViewListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'view.init', array('Cmfcmf\OAuthModule\Listener\ViewListener', 'init'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'view.postfetch', array('Cmfcmf\OAuthModule\Listener\ViewListener', 'postFetch'));
    
        // user login -> Cmfcmf\OAuthModule\Listener\UserLoginListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.ui.login.started', array('Cmfcmf\OAuthModule\Listener\UserLoginListener', 'started'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.ui.login.veto', array('Cmfcmf\OAuthModule\Listener\UserLoginListener', 'veto'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.ui.login.succeeded', array('Cmfcmf\OAuthModule\Listener\UserLoginListener', 'succeeded'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.ui.login.failed', array('Cmfcmf\OAuthModule\Listener\UserLoginListener', 'failed'));
    
        // user logout -> Cmfcmf\OAuthModule\Listener\UserLogoutListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.ui.logout.succeeded', array('Cmfcmf\OAuthModule\Listener\UserLogoutListener', 'succeeded'));
    
        // user -> Cmfcmf\OAuthModule\Listener\UserListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'user.gettheme', array('Cmfcmf\OAuthModule\Listener\UserListener', 'getTheme'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'user.account.create', array('Cmfcmf\OAuthModule\Listener\UserListener', 'create'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'user.account.update', array('Cmfcmf\OAuthModule\Listener\UserListener', 'update'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'user.account.delete', array('Cmfcmf\OAuthModule\Listener\UserListener', 'delete'));
    
        // registration -> Cmfcmf\OAuthModule\Listener\UserRegistrationListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.ui.registration.started', array('Cmfcmf\OAuthModule\Listener\UserRegistrationListener', 'started'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.ui.registration.succeeded', array('Cmfcmf\OAuthModule\Listener\UserRegistrationListener', 'succeeded'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.ui.registration.failed', array('Cmfcmf\OAuthModule\Listener\UserRegistrationListener', 'failed'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'user.registration.create', array('Cmfcmf\OAuthModule\Listener\UserRegistrationListener', 'create'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'user.registration.update', array('Cmfcmf\OAuthModule\Listener\UserRegistrationListener', 'update'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'user.registration.delete', array('Cmfcmf\OAuthModule\Listener\UserRegistrationListener', 'delete'));
    
        // users module -> Cmfcmf\OAuthModule\Listener\UsersListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.users.config.updated', array('Cmfcmf\OAuthModule\Listener\UsersListener', 'configUpdated'));
    
        // group -> Cmfcmf\OAuthModule\Listener\GroupListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'group.create', array('Cmfcmf\OAuthModule\Listener\GroupListener', 'create'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'group.update', array('Cmfcmf\OAuthModule\Listener\GroupListener', 'update'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'group.delete', array('Cmfcmf\OAuthModule\Listener\GroupListener', 'delete'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'group.adduser', array('Cmfcmf\OAuthModule\Listener\GroupListener', 'addUser'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'group.removeuser', array('Cmfcmf\OAuthModule\Listener\GroupListener', 'removeUser'));
    
        // special purposes and 3rd party api support -> Cmfcmf\OAuthModule\Listener\ThirdPartyListener
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'get.pending_content', array('Cmfcmf\OAuthModule\Listener\ThirdPartyListener', 'pendingContentListener'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.content.gettypes', array('Cmfcmf\OAuthModule\Listener\ThirdPartyListener', 'contentGetTypes'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'module.scribite.editorhelpers', array('Cmfcmf\OAuthModule\Listener\ThirdPartyListener', 'getEditorHelpers'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'moduleplugin.tinymce.externalplugins', array('Cmfcmf\OAuthModule\Listener\ThirdPartyListener', 'getTinyMcePlugins'));
        EventUtil::registerPersistentModuleHandler('CmfcmfOAuthModule', 'moduleplugin.ckeditor.externalplugins', array('Cmfcmf\OAuthModule\Listener\ThirdPartyListener', 'getCKEditorPlugins'));
    }
}
