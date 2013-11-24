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

namespace Cmfcmf\OAuthModule\ContentType\Base;

use Cmfcmf\OAuthModule\Util\ControllerUtil;

use ModUtil;
use SecurityUtil;
use ServiceUtil;
use Zikula_View;
use ZLanguage;

/**
 * Generic item list content plugin base class.
 */
class ItemList extends \Content_AbstractContentType
{
    /**
     * The treated object type.
     *
     * @var string
     */
    protected $objectType;
    
    /**
     * The sorting criteria.
     *
     * @var string
     */
    protected $sorting;
    
    /**
     * The amount of desired items.
     *
     * @var integer
     */
    protected $amount;
    
    /**
     * Name of template file.
     *
     * @var string
     */
    protected $template;
    
    /**
     * Name of custom template file.
     *
     * @var string
     */
    protected $customTemplate;
    
    /**
     * Optional filters.
     *
     * @var string
     */
    protected $filter;
    
    /**
     * Returns the module providing this content type.
     *
     * @return string The module name.
     */
    public function getModule()
    {
        return 'CmfcmfOAuthModule';
    }
    
    /**
     * Returns the name of this content type.
     *
     * @return string The content type name.
     */
    public function getName()
    {
        return 'ItemList';
    }
    
    /**
     * Returns the title of this content type.
     *
     * @return string The content type title.
     */
    public function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('CmfcmfOAuthModule');
    
        return __('CmfcmfOAuthModule list view', $dom);
    }
    
    /**
     * Returns the description of this content type.
     *
     * @return string The content type description.
     */
    public function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('CmfcmfOAuthModule');
    
        return __('Display list of CmfcmfOAuthModule objects.', $dom);
    }
    
    /**
     * Loads the data.
     *
     * @param array $data Data array with parameters.
     */
    public function loadData(&$data)
    {
        $serviceManager = ServiceUtil::getManager();
        $controllerHelper = new ControllerUtil($serviceManager, ModUtil::getModule($this->name));
    
        $utilArgs = array('name' => 'list');
        if (!isset($data['objectType']) || !in_array($data['objectType'], $controllerHelper->getObjectTypes('contentType', $utilArgs))) {
            $data['objectType'] = $controllerHelper->getDefaultObjectType('contentType', $utilArgs);
        }
    
        $this->objectType = $data['objectType'];
    
        if (!isset($data['sorting'])) {
            $data['sorting'] = 'default';
        }
        if (!isset($data['amount'])) {
            $data['amount'] = 1;
        }
        if (!isset($data['template'])) {
            $data['template'] = 'itemlist_' . $this->objectType . '_display.tpl';
        }
        if (!isset($data['customTemplate'])) {
            $data['customTemplate'] = '';
        }
        if (!isset($data['filter'])) {
            $data['filter'] = '';
        }
    
        $this->sorting = $data['sorting'];
        $this->amount = $data['amount'];
        $this->template = $data['template'];
        $this->customTemplate = $data['customTemplate'];
        $this->filter = $data['filter'];
    }
    
    /**
     * Displays the data.
     *
     * @return string The returned output.
     */
    public function display()
    {
        $dom = ZLanguage::getModuleDomain('CmfcmfOAuthModule');
        ModUtil::initOOModule('CmfcmfOAuthModule');
    
        $entityClass = '\\Cmfcmf\\OAuthModule\\Entity\\' . ucwords($this->objectType) . 'Entity';
        $serviceManager = ServiceUtil::getManager();
        $entityManager = $serviceManager->getService('doctrine.entitymanager');
        $repository = $entityManager->getRepository($entityClass);
    
        // ensure that the view does not look for templates in the Content module (#218)
        $this->view->toplevelmodule = 'CmfcmfOAuthModule';
    
        $this->view->setCaching(Zikula_View::CACHE_ENABLED);
        // set cache id
        $component = 'CmfcmfOAuthModule:' . ucwords($this->objectType) . ':';
        $instance = '::';
        $accessLevel = ACCESS_READ;
        if (SecurityUtil::checkPermission($component, $instance, ACCESS_COMMENT)) {
            $accessLevel = ACCESS_COMMENT;
        }
        if (SecurityUtil::checkPermission($component, $instance, ACCESS_EDIT)) {
            $accessLevel = ACCESS_EDIT;
        }
        $this->view->setCacheId('view|ot_' . $this->objectType . '_sort_' . $this->sorting . '_amount_' . $this->amount . '_' . $accessLevel);
    
        $template = $this->getDisplayTemplate();
    
        // if page is cached return cached content
        if ($this->view->is_cached($template)) {
            return $this->view->fetch($template);
        }
    
        // create query
        $where = $this->filter;
        $orderBy = $this->getSortParam($repository);
        $qb = $repository->genericBaseQuery($where, $orderBy);
    
        // get objects from database
        $currentPage = 1;
        $resultsPerPage = (isset($this->amount) ? $this->amount : 1);
        list($query, $count) = $repository->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
        $entities = $query->getResult();
    
        $data = array('objectType' => $this->objectType,
                      'catids' => $this->catIds,
                      'sorting' => $this->sorting,
                      'amount' => $this->amount,
                      'template' => $this->template,
                      'customTemplate' => $this->customTemplate,
                      'filter' => $this->filter);
    
        // assign block vars and fetched data
        $this->view->assign('vars', $data)
                   ->assign('objectType', $this->objectType)
                   ->assign('items', $entities)
                   ->assign($repository->getAdditionalTemplateParameters('contentType'));
    
        $output = $this->view->fetch($template);
    
        return $output;
    }
    
    /**
     * Returns the template used for output.
     *
     * @return string the template path.
     */
    protected function getDisplayTemplate()
    {
        $templateFile = $this->template;
        if ($templateFile == 'custom') {
            $templateFile = $this->customTemplate;
        }
    
        $templateForObjectType = str_replace('itemlist_', 'itemlist_' . $this->objectType . '_', $templateFile);
    
        $template = '';
        if ($this->view->template_exists('ContentType/' . $templateForObjectType)) {
            $template = 'ContentType/' . $templateForObjectType;
        } elseif ($this->view->template_exists('ContentType/' . $templateFile)) {
            $template = 'ContentType/' . $templateFile;
        } else {
            $template = 'ContentType/itemlist_display.tpl';
        }
    
        return $template;
    }
    
    /**
     * Determines the order by parameter for item selection.
     *
     * @param Doctrine_Repository $repository The repository used for data fetching.
     *
     * @return string the sorting clause.
     */
    protected function getSortParam($repository)
    {
        if ($this->sorting == 'random') {
            return 'RAND()';
        }
    
        $sortParam = '';
        if ($this->sorting == 'newest') {
            $idFields = ModUtil::apiFunc('CmfcmfOAuthModule', 'selection', 'getIdFields', array('ot' => $this->objectType));
            if (count($idFields) == 1) {
                $sortParam = $idFields[0] . ' DESC';
            } else {
                foreach ($idFields as $idField) {
                    if (!empty($sortParam)) {
                        $sortParam .= ', ';
                    }
                    $sortParam .= $idField . ' ASC';
                }
            }
        } elseif ($this->sorting == 'default') {
            $sortParam = $repository->getDefaultSortingField() . ' ASC';
        }
    
        return $sortParam;
    }
    
    /**
     * Displays the data for editing.
     */
    public function displayEditing()
    {
        return $this->display();
    }
    
    /**
     * Returns the default data.
     *
     * @return array Default data and parameters.
     */
    public function getDefaultData()
    {
        return array('objectType' => 'user',
                     'sorting' => 'default',
                     'amount' => 1,
                     'template' => 'itemlist_display.tpl',
                     'customTemplate' => '',
                     'filter' => '');
    }
    
    /**
     * Executes additional actions for the editing mode.
     */
    public function startEditing()
    {
        // ensure that the view does not look for templates in the Content module (#218)
        $this->view->toplevelmodule = 'CmfcmfOAuthModule';
    
        // ensure our custom plugins are loaded
        array_push($this->view->plugins_dir, 'modules/Cmfcmf/OAuthModule/Resources/views/»/plugins');
    }
}
