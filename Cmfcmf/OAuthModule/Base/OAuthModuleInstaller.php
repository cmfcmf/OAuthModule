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
        $this->setVar('suggestRegistrationOnFailedLogin', false);
        $this->setVar('useMaximumInformationForRegistration', false);
    
        $categoryRegistryIdsPerEntity = array();
    
        // create the default data
        $this->createDefaultData($categoryRegistryIdsPerEntity);
    
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
        // The following convenience methods are each responsible for a single aspect of upgrading to Zikula 1.3.7.
    
        // here is a possible usage example
        // of course 1.2.3 should match the number you used for the last stable 1.3.5/1.3.6 module version.
        /* if ($oldVersion = 1.2.3) {
            // rename module for all modvars
            $this->updateModVarsTo137();
            
            // update extension information about this app
            $this->updateExtensionInfoFor137();
            
            // rename existing permission rules
            $this->renamePermissionsFor137();
            
            // rename existing category registries
            $this->renameCategoryRegistriesFor137();
            
            // rename all tables
            $this->renameTablesFor137();
            
            // remove event handler definitions from database
            $this->dropEventHandlersFromDatabase();
            
            // update module name in the hook tables
            $this->updateHookNamesFor137();
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
        $classNames[] = 'Cmfcmf\OAuthModule\Entity\MappedIdEntity';
    
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
        $entityClass = 'CmfcmfOAuthModule:MappedIdEntity';
        $this->entityManager->getRepository($entityClass)->truncateTable();
    }
}