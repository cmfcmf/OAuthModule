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

namespace Cmfcmf\OAuthModule\Controller\Base;

use Cmfcmf\OAuthModule\Form\Handler\Admin\ConfigHandler;
use Cmfcmf\OAuthModule\Util\ControllerUtil;
use Cmfcmf\OAuthModule\Util\ModelUtil;
use Cmfcmf\OAuthModule\Util\ViewUtil;
use Cmfcmf\OAuthModule\Util\WorkflowUtil;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FormUtil;
use LogUtil;
use ModUtil;
use SecurityUtil;
use System;
use Zikula_AbstractController;
use Zikula_View;
use ZLanguage;
use Zikula\Core\Hook\ProcessHook;
use Zikula\Core\Hook\ValidationHook;
use Zikula\Core\Hook\ValidationProviders;
use Zikula\Core\ModUrl;
use Zikula\Core\Response\PlainResponse;

/**
 * Admin controller class.
 */
class AdminController extends Zikula_AbstractController
{
    /**
     * Post initialise.
     *
     * Run after construction.
     *
     * @return void
     */
    protected function postInitialize()
    {
        // Set caching to false by default.
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
    }

    /**
     * This method is the default function handling the admin area called without defining arguments.
     *
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedHttpException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedHttpException();
        }
        return $this->redirect(ModUtil::url($this->name, 'admin', 'view'));
    }
    
    /**
     * This method provides a generic item list overview.
     *
     * @param string  $ot           Treated object type.
     * @param string  $sort         Sorting field.
     * @param string  $sortdir      Sorting direction.
     * @param int     $pos          Current pager position.
     * @param int     $num          Amount of entries to display.
     * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
     * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedHttpException Thrown if the user doesn't have required permissions
     */
    public function viewAction(Request $request)
    {
        $controllerHelper = new ControllerUtil($this->serviceManager, ModUtil::getModule($this->name));
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->filter('ot', 'user', false, FILTER_SANITIZE_STRING);
        $utilArgs = array('controller' => 'admin', 'action' => 'view');
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        if (!SecurityUtil::checkPermission($this->name . ':' . ucwords($objectType) . ':', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedHttpException();
        }
        $entityClass = '\\Cmfcmf\\OAuthModule\\Entity\\' . ucwords($objectType) . 'Entity';
        $repository = $this->entityManager->getRepository($entityClass);
        $repository->setRequest($this->request);
        $viewHelper = new ViewUtil($this->serviceManager, ModUtil::getModule($this->name));
        
        // parameter for used sorting field
        $sort = $request->query->filter('sort', '', false, FILTER_SANITIZE_STRING);
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
        }
        
        // parameter for used sort order
        $sdir = $request->query->filter('sortdir', '', false, FILTER_SANITIZE_STRING);
        $sdir = strtolower($sdir);
        if ($sdir != 'asc' && $sdir != 'desc') {
            $sdir = 'asc';
        }
        
        // convenience vars to make code clearer
        $currentUrlArgs = array('ot' => $objectType);
        
        $where = '';
        
        $selectionArgs = array(
            'ot' => $objectType,
            'where' => $where,
            'orderBy' => $sort . ' ' . $sdir
        );
        
        $showOwnEntries = (int) $request->query->filter('own', $this->getVar('showOnlyOwnEntries', 0), false, FILTER_VALIDATE_INT);
        $showAllEntries = (int) $request->query->filter('all', 0, false, FILTER_VALIDATE_INT);
        
        if (!$showAllEntries) {
            $csv = (int) $request->query->filter('usecsvext', 0, false, FILTER_VALIDATE_INT);
            if ($csv == 1) {
                $showAllEntries = 1;
            }
        }
        
        $this->view->assign('showOwnEntries', $showOwnEntries)
                   ->assign('showAllEntries', $showAllEntries);
        if ($showOwnEntries == 1) {
            $currentUrlArgs['own'] = 1;
        }
        if ($showAllEntries == 1) {
            $currentUrlArgs['all'] = 1;
        }
        
        // prepare access level for cache id
        $accessLevel = ACCESS_READ;
        $component = 'CmfcmfOAuthModule:' . ucwords($objectType) . ':';
        $instance = '::';
        if (SecurityUtil::checkPermission($component, $instance, ACCESS_COMMENT)) {
            $accessLevel = ACCESS_COMMENT;
        }
        if (SecurityUtil::checkPermission($component, $instance, ACCESS_EDIT)) {
            $accessLevel = ACCESS_EDIT;
        }
        
        $templateFile = $viewHelper->getViewTemplate($this->view, 'admin', $objectType, 'view', $request);
        $cacheId = 'view|ot_' . $objectType . '_sort_' . $sort . '_' . $sdir;
        $resultsPerPage = 0;
        if ($showAllEntries == 1) {
            // set cache id
            $this->view->setCacheId($cacheId . '_all_1_own_' . $showOwnEntries . '_' . $accessLevel);
        
            // if page is cached return cached content
            if ($this->view->is_cached($templateFile)) {
                return $viewHelper->processTemplate($this->view, 'admin', $objectType, 'view', $request, $templateFile);
            }
        
            // retrieve item list without pagination
            $entities = ModUtil::apiFunc($this->name, 'selection', 'getEntities', $selectionArgs);
        } else {
            // the current offset which is used to calculate the pagination
            $currentPage = (int) $request->query->filter('pos', 1, false, FILTER_VALIDATE_INT);
        
            // the number of items displayed on a page for pagination
            $resultsPerPage = (int) $request->query->filter('num', 0, false, FILTER_VALIDATE_INT);
            if ($resultsPerPage == 0) {
                $resultsPerPage = $this->getVar('pageSize', 10);
            }
        
            // set cache id
            $this->view->setCacheId($cacheId . '_amount_' . $resultsPerPage . '_page_' . $currentPage . '_own_' . $showOwnEntries . '_' . $accessLevel);
        
            // if page is cached return cached content
            if ($this->view->is_cached($templateFile)) {
                return $viewHelper->processTemplate($this->view, 'admin', $objectType, 'view', $request, $templateFile);
            }
        
            // retrieve item list with pagination
            $selectionArgs['currentPage'] = $currentPage;
            $selectionArgs['resultsPerPage'] = $resultsPerPage;
            list($entities, $objectCount) = ModUtil::apiFunc($this->name, 'selection', 'getEntitiesPaginated', $selectionArgs);
        
            $this->view->assign('currentPage', $currentPage)
                       ->assign('pager', array('numitems'     => $objectCount,
                                               'itemsperpage' => $resultsPerPage));
        }
        
        foreach ($entities as $k => $entity) {
            $entity->initWorkflow();
        }
        
        // build ModUrl instance for display hooks
        $currentUrlObject = new ModUrl($this->name, 'admin', 'view', ZLanguage::getLanguageCode(), $currentUrlArgs);
        
        // assign the object data, sorting information and details for creating the pager
        $this->view->assign('items', $entities)
                   ->assign('sort', $sort)
                   ->assign('sdir', $sdir)
                   ->assign('pageSize', $resultsPerPage)
                   ->assign('currentUrlObject', $currentUrlObject)
                   ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));
        
        $modelHelper = new ModelUtil($this->serviceManager, ModUtil::getModule($this->name));
        $this->view->assign('canBeCreated', $modelHelper->canBeCreated($objectType));
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($this->view, 'admin', $objectType, 'view', $request, $templateFile);
    }
    
    /**
     * This method provides a generic handling of simple delete requests.
     *
     * @param string  $ot           Treated object type.
     * @param int     $id           Identifier of entity to be deleted.
     * @param boolean $confirmation Confirm the deletion, else a confirmation page is displayed.
     * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
     * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
     *
     * @return mixed Output.
     *
     * @throws AccessDeniedHttpException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException     Thrown if item to be deleted isn't found
     */
    public function deleteAction(Request $request)
    {
        $controllerHelper = new ControllerUtil($this->serviceManager, ModUtil::getModule($this->name));
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->filter('ot', 'user', false, FILTER_SANITIZE_STRING);
        $utilArgs = array('controller' => 'admin', 'action' => 'delete');
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        if (!SecurityUtil::checkPermission($this->name . ':' . ucwords($objectType) . ':', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedHttpException();
        }
        $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', array('ot' => $objectType));
        
        // retrieve identifier of the object we wish to delete
        $idValues = $controllerHelper->retrieveIdentifier($this->request, array(), $objectType, $idFields);
        $hasIdentifier = $controllerHelper->isValidIdentifier($idValues);
        
        if (!$hasIdentifier) {
            throw new NotFoundHttpException($this->__('Error! Invalid identifier received.'));
        }
        
        $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', array('ot' => $objectType, 'id' => $idValues));
        if ($entity === null) {
            throw new NotFoundHttpException($this->__('No such item.'));
        }
        
        $entity->initWorkflow();
        
        $workflowHelper = new WorkflowUtil($this->serviceManager, ModUtil::getModule($this->name));
        $deleteActionId = 'delete';
        $deleteAllowed = false;
        $actions = $workflowHelper->getActionsForObject($entity);
        if ($actions === false || !is_array($actions)) {
            throw new \RuntimeException($this->__('Error! Could not determine workflow actions.'));
        }
        foreach ($actions as $actionId => $action) {
            if ($actionId != $deleteActionId) {
                continue;
            }
            $deleteAllowed = true;
            break;
        }
        if (!$deleteAllowed) {
            throw new \RuntimeException($this->__('Error! It is not allowed to delete this entity.'));
        }
        
        $confirmation = (bool) $this->request->request->filter('confirmation', false, false, FILTER_VALIDATE_BOOLEAN);
        if ($confirmation) {
            $this->checkCsrfToken();
        
            $hookAreaPrefix = $entity->getHookAreaPrefix();
            $hookType = 'validate_delete';
            // Let any hooks perform additional validation actions
            $hook = new ValidationHook(new ValidationProviders());
            $validators = $this->dispatchHooks($hookAreaPrefix . '.' . $hookType, $hook)->getValidators();
            if (!$validators->hasErrors()) {
                // execute the workflow action
                $success = $workflowHelper->executeAction($entity, $deleteActionId);
                if ($success) {
                    $this->registerStatus($this->__('Done! Item deleted.'));
                }
        
                // Let any hooks know that we have created, updated or deleted an item
                $hookType = 'process_delete';
                $hook = new ProcessHook($entity->createCompositeIdentifier());
                $this->dispatchHooks($hookAreaPrefix . '.' . $hookType, $hook);
        
                // An item was deleted, so we clear all cached pages this item.
                $cacheArgs = array('ot' => $objectType, 'item' => $entity);
                ModUtil::apiFunc($this->name, 'cache', 'clearItemCache', $cacheArgs);
        
                // redirect to the list of the current object type
                $this->redirect(ModUtil::url($this->name, 'admin', 'view',
                                                                                            array('ot' => $objectType)));
            }
        }
        
        $entityClass = '\\Cmfcmf\\OAuthModule\\Entity\\' . ucwords($objectType) . 'Entity';
        $repository = $this->entityManager->getRepository($entityClass);
        
        // set caching id
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
        
        // assign the object we loaded above
        $this->view->assign($objectType, $entity)
                   ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));
        
        // fetch and return the appropriate template
        $viewHelper = new ViewUtil($this->serviceManager, ModUtil::getModule($this->name));
        
        return $viewHelper->processTemplate($this->view, 'admin', $objectType, 'delete', $request);
    }
    

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param string $ot     Name of currently used object type.
     * @param string $action The action to be executed.
     * @param array  $items  Identifier list of the items to be processed.
     *
     * @return bool true on sucess, false on failure.
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        $this->checkCsrfToken();
    
        $returnUrl = ModUtil::url($this->name, 'admin', 'index');
    
        // Determine object type
        $objectType = $request->request->get('ot', '');
        if (!$objectType) {
            return System::redirect($returnUrl);
        }
        $returnUrl = ModUtil::url($this->name, 'admin', 'view', array('ot' => $objectType));
    
        // Get other parameters
        $action = $request->request->get('action', null);
        $action = strtolower($action);
        $items = $request->request->get('items', null);
    
        $workflowHelper = new WorkflowUtil($this->serviceManager, ModUtil::getModule($this->name));
    
        // process each item
        foreach ($items as $itemid) {
            // check if item exists, and get record instance
            $selectionArgs = array('ot' => $objectType,
                                   'id' => $itemid,
                                   'useJoins' => false);
            $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', $selectionArgs);
    
            $entity->initWorkflow();
    
            // check if $action can be applied to this entity (may depend on it's current workflow state)
            $allowedActions = $workflowHelper->getActionsForObject($entity);
            $actionIds = array_keys($allowedActions);
            if (!in_array($action, $actionIds)) {
                // action not allowed, skip this object
                continue;
            }
    
            $hookAreaPrefix = $entity->getHookAreaPrefix();
    
            // Let any hooks perform additional validation actions
            $hookType = $action == 'delete' ? 'validate_delete' : 'validate_edit';
            $hook = new ValidationHook(new ValidationProviders());
            $validators = $this->dispatchHooks($hookAreaPrefix . '.' . $hookType, $hook)->getValidators();
            if ($validators->hasErrors()) {
                continue;
            }
    
            $success = false;
            try {
                // execute the workflow action
                $success = $workflowHelper->executeAction($entity, $action);
            } catch(\Exception $e) {
                throw new \RuntimeException($this->__f('Sorry, but an unknown error occured during the %s action. Please apply the changes again!', array($action)));
            }
    
            if (!$success) {
                continue;
            }
    
            if ($action == 'delete') {
                LogUtil::registerStatus($this->__('Done! Item deleted.'));
            } else {
                LogUtil::registerStatus($this->__('Done! Item updated.'));
            }
    
            // Let any hooks know that we have updated or deleted an item
            $hookType = $action == 'delete' ? 'process_delete' : 'process_edit';
            $url = null;
            if ($action != 'delete') {
                $urlArgs = $entity->createUrlArgs();
                $url = new ModUrl($this->name, 'admin', 'display', ZLanguage::getLanguageCode(), $urlArgs);
            }
            $hook = new ProcessHook($entity->createCompositeIdentifier(), $url);
            $this->dispatchHooks($hookAreaPrefix . '.' . $hookType, $hook);
    
            // An item was updated or deleted, so we clear all cached pages for this item.
            $cacheArgs = array('ot' => $objectType, 'item' => $entity);
            ModUtil::apiFunc($this->name, 'cache', 'clearItemCache', $cacheArgs);
        }
    
        // clear view cache to reflect our changes
        $this->view->clear_cache();
    
        return System::redirect($returnUrl);
    }

    /**
     * This method takes care of the application configuration.
     *
     * @return string Output
     *
     * @throws AccessDeniedHttpException Thrown if the user doesn't have required permissions
     */
    public function configAction()
    {
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedHttpException();
        }

        // Create new Form reference
        $view = FormUtil::newForm($this->name, $this);

        $templateName = 'Admin/config.tpl';

        // Execute form using supplied template and page event handler
        return $view->execute($templateName, new ConfigHandler());
    }
}
