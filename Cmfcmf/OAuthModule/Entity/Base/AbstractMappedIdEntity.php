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

namespace Cmfcmf\OAuthModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\StandardFields\Mapping\Annotation as ZK;

use Cmfcmf\OAuthModule\Util\WorkflowUtil;

use DataUtil;
use FormUtil;
use LogUtil;
use ModUtil;
use SecurityUtil;
use ServiceUtil;
use System;
use UserUtil;
use Zikula_EntityAccess;
use Zikula_Exception;
use Zikula_Workflow_Util;
use ZLanguage;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for mapped id entities.
 *
 * @abstract
 */
abstract class AbstractMappedIdEntity extends Zikula_EntityAccess
{
    /**
     * @var string The tablename this object maps to.
     */
    protected $_objectType = 'mappedId';
    
    /**
     * @var \Cmfcmf\OAuthModule\Entity\Validator\MappedIdValidator The validator for this entity.
     */
    protected $_validator = null;
    
    /**
     * @var boolean Option to bypass validation if needed.
     */
    protected $_bypassValidation = false;
    
    /**
     * @var array List of available item actions.
     */
    protected $_actions = array();
    
    /**
     * @var array The current workflow data of this object.
     */
    protected $__WORKFLOW__ = array();
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id.
     */
    protected $id = 0;
    
    /**
     * @ORM\Column(length=20)
     * @var string $workflowState.
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(type="bigint")
     * @var integer $userId.
     */
    protected $userId = 0;
    
    /**
     * @ORM\Column(type="text", length=1000)
     * @var text $claimedId.
     */
    protected $claimedId = '';
    
    /**
     * @ORM\Column(length=255)
     * @var string $provider.
     */
    protected $provider = '';
    
    
    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="create")
     * @var integer $createdUserId.
     */
    protected $createdUserId;
    
    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="update")
     * @var integer $updatedUserId.
     */
    protected $updatedUserId;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var datetime $createdDate.
     */
    protected $createdDate;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @var datetime $updatedDate.
     */
    protected $updatedDate;
    
    
    /**
     * Constructor.
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     *
     * @param TODO
     */
    public function __construct()
    {
        $this->userId = UserUtil::getVar('uid');
        $this->workflowState = 'initial';
        $this->initValidator();
        $this->initWorkflow();
    }
    
    /**
     * Get _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Set _object type.
     *
     * @param string $_objectType.
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        $this->_objectType = $_objectType;
    }
    
    /**
     * Get _validator.
     *
     * @return \Cmfcmf\OAuthModule\Entity\Validator\MappedIdValidator
     */
    public function get_validator()
    {
        return $this->_validator;
    }
    
    /**
     * Set _validator.
     *
     * @param \Cmfcmf\OAuthModule\Entity\Validator\MappedIdValidator $_validator.
     *
     * @return void
     */
    public function set_validator(\Cmfcmf\OAuthModule\Entity\Validator\MappedIdValidator $_validator = null)
    {
        $this->_validator = $_validator;
    }
    
    /**
     * Get _bypass validation.
     *
     * @return boolean
     */
    public function get_bypassValidation()
    {
        return $this->_bypassValidation;
    }
    
    /**
     * Set _bypass validation.
     *
     * @param boolean $_bypassValidation.
     *
     * @return void
     */
    public function set_bypassValidation($_bypassValidation)
    {
        $this->_bypassValidation = $_bypassValidation;
    }
    
    /**
     * Get _actions.
     *
     * @return array
     */
    public function get_actions()
    {
        return $this->_actions;
    }
    
    /**
     * Set _actions.
     *
     * @param array $_actions.
     *
     * @return void
     */
    public function set_actions(array $_actions = Array())
    {
        $this->_actions = $_actions;
    }
    
    /**
     * Get __ w o r k f l o w__.
     *
     * @return array
     */
    public function get__WORKFLOW__()
    {
        return $this->__WORKFLOW__;
    }
    
    /**
     * Set __ w o r k f l o w__.
     *
     * @param array $__WORKFLOW__.
     *
     * @return void
     */
    public function set__WORKFLOW__(array $__WORKFLOW__ = Array())
    {
        $this->__WORKFLOW__ = $__WORKFLOW__;
    }
    
    
    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set id.
     *
     * @param integer $id.
     *
     * @return void
     */
    public function setId($id)
    {
        if ($id != $this->id) {
            $this->id = $id;
        }
    }
    
    /**
     * Get workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Set workflow state.
     *
     * @param string $workflowState.
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        if ($workflowState != $this->workflowState) {
            $this->workflowState = $workflowState;
        }
    }
    
    /**
     * Get user id.
     *
     * @return bigint
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * Set user id.
     *
     * @param bigint $userId.
     *
     * @return void
     */
    public function setUserId($userId)
    {
        if ($userId != $this->userId) {
            $this->userId = $userId;
        }
    }
    
    /**
     * Get claimed id.
     *
     * @return text
     */
    public function getClaimedId()
    {
        return $this->claimedId;
    }
    
    /**
     * Set claimed id.
     *
     * @param text $claimedId.
     *
     * @return void
     */
    public function setClaimedId($claimedId)
    {
        if ($claimedId != $this->claimedId) {
            $this->claimedId = $claimedId;
        }
    }
    
    /**
     * Get provider.
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }
    
    /**
     * Set provider.
     *
     * @param string $provider.
     *
     * @return void
     */
    public function setProvider($provider)
    {
        if ($provider != $this->provider) {
            $this->provider = $provider;
        }
    }
    
    /**
     * Get created user id.
     *
     * @return integer
     */
    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }
    
    /**
     * Set created user id.
     *
     * @param integer $createdUserId.
     *
     * @return void
     */
    public function setCreatedUserId($createdUserId)
    {
        $this->createdUserId = $createdUserId;
    }
    
    /**
     * Get updated user id.
     *
     * @return integer
     */
    public function getUpdatedUserId()
    {
        return $this->updatedUserId;
    }
    
    /**
     * Set updated user id.
     *
     * @param integer $updatedUserId.
     *
     * @return void
     */
    public function setUpdatedUserId($updatedUserId)
    {
        $this->updatedUserId = $updatedUserId;
    }
    
    /**
     * Get created date.
     *
     * @return datetime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
    
    /**
     * Set created date.
     *
     * @param datetime $createdDate.
     *
     * @return void
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }
    
    /**
     * Get updated date.
     *
     * @return datetime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }
    
    /**
     * Set updated date.
     *
     * @param datetime $updatedDate.
     *
     * @return void
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }
    
    
    
    /**
     * Initialise validator and return it's instance.
     *
     * @return \Cmfcmf\OAuthModule\Entity\Validator\MappedIdValidator The validator for this entity.
     */
    public function initValidator()
    {
        if (!is_null($this->_validator)) {
            return $this->_validator;
        }
        $this->_validator = new \Cmfcmf\OAuthModule\Entity\Validator\MappedIdValidator($this);
    
        return $this->_validator;
    }
    
    /**
     * Sets/retrieves the workflow details.
     *
     * @throws RuntimeException Thrown if retrieving the workflow object fails
     */
    public function initWorkflow()
    {
        $currentFunc = FormUtil::getPassedValue('func', 'index', 'GETPOST', FILTER_SANITIZE_STRING);
        $isReuse = FormUtil::getPassedValue('astemplate', '', 'GETPOST', FILTER_SANITIZE_STRING);
    
        // apply workflow with most important information
        $idColumn = 'id';
        $workflowHelper = new WorkflowUtil(ServiceUtil::getManager(), ModUtil::getModule('CmfcmfOAuthModule'));
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = array(
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => $idColumn,
            'obj_id' => $this[$idColumn],
            'schemaname' => $schemaName);
        
        // load the real workflow only when required (e. g. when func is edit or delete)
        if (!in_array($currentFunc, array('index', 'view', 'display')) && empty($isReuse)) {
            $result = Zikula_Workflow_Util::getWorkflowForObject($this, $this['_objectType'], $idColumn, 'CmfcmfOAuthModule');
            if (!$result) {
                $dom = ZLanguage::getModuleDomain('CmfcmfOAuthModule');
                throw new \RuntimeException(__('Error! Could not load the associated workflow.', $dom));
            }
        }
        
        if (!is_object($this['__WORKFLOW__']) && !isset($this['__WORKFLOW__']['schemaname'])) {
            $workflow = $this['__WORKFLOW__'];
            $workflow['schemaname'] = $schemaName;
            $this['__WORKFLOW__'] = $workflow;
        }
    }
    
    /**
     * Resets workflow data back to initial state.
     * To be used after cloning an entity object.
     */
    public function resetWorkflow()
    {
        $this->setWorkflowState('initial');
        $workflowHelper = new WorkflowUtil(ServiceUtil::getManager(), ModUtil::getModule('CmfcmfOAuthModule'));
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = array(
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => 'id',
            'obj_id' => 0,
            'schemaname' => $schemaName);
    }
    
    /**
     * Start validation and raise exception if invalid data is found.
     *
     * @return void.
     *
     * @throws Zikula_Exception Thrown if a validation error occurs
     */
    public function validate()
    {
        if ($this->_bypassValidation === true) {
            return;
        }
    
        $result = $this->initValidator()->validateAll();
        if (is_array($result)) {
            throw new Zikula_Exception($result['message'], $result['code'], $result['debugArray']);
        }
    }
    
    /**
     * Return entity data in JSON format.
     *
     * @return string JSON-encoded data.
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    /**
     * Collect available actions for this entity.
     */
    protected function prepareItemActions()
    {
        if (!empty($this->_actions)) {
            return;
        }
    
        $currentType = FormUtil::getPassedValue('type', 'user', 'GETPOST', FILTER_SANITIZE_STRING);
        $currentFunc = FormUtil::getPassedValue('func', 'index', 'GETPOST', FILTER_SANITIZE_STRING);
        $dom = ZLanguage::getModuleDomain('CmfcmfOAuthModule');
        if ($currentType == 'admin') {
            if (in_array($currentFunc, array('index', 'view'))) {
            }
            if (in_array($currentFunc, array('index', 'view', 'display'))) {
                $component = 'CmfcmfOAuthModule:MappedId:';
                $instance = $this->id . '::';
                if (SecurityUtil::checkPermission($component, $instance, ACCESS_DELETE)) {
                    $this->_actions[] = array(
                        'url' => array('type' => 'admin', 'func' => 'delete', 'arguments' => array('ot' => 'mappedId', 'id' => $this['id'])),
                        'icon' => 'trash-o',
                        'linkTitle' => __('Delete', $dom),
                        'linkText' => __('Delete', $dom)
                    );
                }
            }
        }
        if ($currentType == 'user') {
            if (in_array($currentFunc, array('index', 'view'))) {
            }
            if (in_array($currentFunc, array('index', 'view', 'display'))) {
                $component = 'CmfcmfOAuthModule:MappedId:';
                $instance = $this->id . '::';
                if (SecurityUtil::checkPermission($component, $instance, ACCESS_DELETE)) {
                    $this->_actions[] = array(
                        'url' => array('type' => 'user', 'func' => 'delete', 'arguments' => array('ot' => 'mappedId', 'id' => $this['id'])),
                        'icon' => 'trash-o',
                        'linkTitle' => __('Delete', $dom),
                        'linkText' => __('Delete', $dom)
                    );
                }
            }
        }
    }
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return Array The resulting arguments list.
     */
    public function createUrlArgs()
    {
        $args = array('ot' => $this['_objectType']);
    
        $args['id'] = $this['id'];
    
        if (isset($this['slug'])) {
            $args['slug'] = $this['slug'];
        }
    
        return $args;
    }
    
    /**
     * Create concatenated identifier string (for composite keys).
     *
     * @return String concatenated identifiers.
     */
    public function createCompositeIdentifier()
    {
        $itemId = $this['id'];
    
        return $itemId;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'oauth.ui_hooks.mappedids';
    }

    
    /**
     * Post-Process the data after the entity has been constructed by the entity manager.
     * The event happens after the entity has been loaded from database or after a refresh call.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - no access to associations (not initialised yet)
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::postLoadCallback()
     * @return boolean true if completed successfully else false.
     *
     * @throws RuntimeException Thrown if upload file base path retrieval fails
     */
    protected function performPostLoadCallback()
    {
        // echo 'loaded a record ...';
        $currentFunc = FormUtil::getPassedValue('func', 'index', 'GETPOST', FILTER_SANITIZE_STRING);
        $usesCsvOutput = FormUtil::getPassedValue('usecsvext', false, 'GETPOST', FILTER_SANITIZE_STRING);
        
        $this['id'] = (int) ((isset($this['id']) && !empty($this['id'])) ? DataUtil::formatForDisplay($this['id']) : 0);
        $this->formatTextualField('workflowState', $currentFunc, $usesCsvOutput, true);
        $this['userId'] = (int) ((isset($this['userId']) && !empty($this['userId'])) ? DataUtil::formatForDisplay($this['userId']) : 0);
        $this->formatTextualField('claimedId', $currentFunc, $usesCsvOutput);
        $this->formatTextualField('provider', $currentFunc, $usesCsvOutput);
    
        $this->prepareItemActions();
    
        return true;
    }
    
    /**
     * Formats a given textual field depending on it's actual kind of content.
     *
     * @param string  $fieldName     Name of field to be formatted.
     * @param string  $currentFunc   Name of current controller action.
     * @param string  $usesCsvOutput Whether the output is CSV or not (defaults to false).
     * @param boolean $allowZero     Whether 0 values are allowed or not (defaults to false).
     */
    protected function formatTextualField($fieldName, $currentFunc, $usesCsvOutput = false, $allowZero = false)
    {
        if ($currentFunc == 'edit') {
            // apply no changes when editing the content
            return;
        }
    
        $string = '';
        if (isset($this[$fieldName])) {
            if (!empty($this[$fieldName]) || ($allowZero && $this[$fieldName] == 0)) {
                $string = $this[$fieldName];
                if ($usesCsvOutput == 1) {
                    // strip only quotes when displaying raw output in CSV
                    $string = str_replace('"', '""', $string);
                } else {
                    if ($this->containsHtml($string)) {
                        $string = DataUtil::formatForDisplayHTML($string);
                    } else {
                        $string = DataUtil::formatForDisplay($string);
                        $string = nl2br($string);
                    }
                }
            }
        }
    
        $this[$fieldName] = $string;
    }
    
    /**
     * Checks whether any html tags are contained in the given string.
     * See http://stackoverflow.com/questions/10778035/how-to-check-if-string-contents-have-any-html-in-it for implementation details.
     *
     * @param $string string The given input string.
     *
     * @return boolean Whether any html tags are found or not.
     */
    protected function containsHtml($string)
    {
        return preg_match("/<[^<]+>/", $string, $m) != 0;
    }
    
    /**
     * Pre-Process the data prior to an insert operation.
     * The event happens before the entity managers persist operation is executed for this entity.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - no identifiers available if using an identity generator like sequences
     *     - Doctrine won't recognize changes on relations which are done here
     *       if this method is called by cascade persist
     *     - no creation of other entities allowed
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::prePersistCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPrePersistCallback()
    {
        // echo 'inserting a record ...';
        $this->validate();
    
        return true;
    }
    
    /**
     * Post-Process the data after an insert operation.
     * The event happens after the entity has been made persistant.
     * Will be called after the database insert operations.
     * The generated primary key values are available.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::postPersistCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostPersistCallback()
    {
        // echo 'inserted a record ...';
        return true;
    }
    
    /**
     * Pre-Process the data prior a delete operation.
     * The event happens before the entity managers remove operation is executed for this entity.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL DELETE statement
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::preRemoveCallback()
     * @return boolean true if completed successfully else false.
     *
     * @throws RuntimeException Thrown if workflow deletion fails
     */
    protected function performPreRemoveCallback()
    {
        // delete workflow for this entity
        $workflowHelper = new WorkflowUtil(ServiceUtil::getManager());
        $workflowHelper->normaliseWorkflowData($this);
        $workflow = $this['__WORKFLOW__'];
        if ($workflow['id'] > 0) {
            $serviceManager = ServiceUtil::getManager();
            $entityManager = $serviceManager->getService('doctrine.entitymanager');
            $result = true;
            try {
                $workflow = $entityManager->find('Zikula\Core\Doctrine\Entity\WorkflowEntity', $workflow['id']);
                $entityManager->remove($workflow);
                $entityManager->flush();
            } catch (\Exception $e) {
                $result = false;
            }
            if ($result === false) {
                $dom = ZLanguage::getModuleDomain('CmfcmfOAuthModule');
                throw new \RuntimeException(__('Error! Could not remove stored workflow. Deletion has been aborted.', $dom));
            }
        }
    
        return true;
    }
    
    /**
     * Post-Process the data after a delete.
     * The event happens after the entity has been deleted.
     * Will be called after the database delete operations.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL DELETE statement
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::postRemoveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostRemoveCallback()
    {
        // echo 'deleted a record ...';
    
        return true;
    }
    
    /**
     * Pre-Process the data prior to an update operation.
     * The event happens before the database update operations for the entity data.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL UPDATE statement
     *     - changes on associations are not allowed and won't be recognized by flush
     *     - changes on properties won't be recognized by flush as well
     *     - no creation of other entities allowed
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::preUpdateCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPreUpdateCallback()
    {
        // echo 'updating a record ...';
        $this->validate();
    
        return true;
    }
    
    /**
     * Post-Process the data after an update operation.
     * The event happens after the database update operations for the entity data.
     *
     * Restrictions:
     *     - no access to entity manager or unit of work apis
     *     - will not be called for a DQL UPDATE statement
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::postUpdateCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostUpdateCallback()
    {
        // echo 'updated a record ...';
        return true;
    }
    
    /**
     * Pre-Process the data prior to a save operation.
     * This combines the PrePersist and PreUpdate events.
     * For more information see corresponding callback handlers.
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::preSaveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPreSaveCallback()
    {
        // echo 'saving a record ...';
        $this->validate();
    
        return true;
    }
    
    /**
     * Post-Process the data after a save operation.
     * This combines the PostPersist and PostUpdate events.
     * For more information see corresponding callback handlers.
     *
     * @see Cmfcmf\OAuthModule\Entity\MappedIdEntity::postSaveCallback()
     * @return boolean true if completed successfully else false.
     */
    protected function performPostSaveCallback()
    {
        // echo 'saved a record ...';
        return true;
    }
    

    /**
     * Returns the formatted title conforming to the display pattern
     * specified for this entity.
     */
    public function getTitleFromDisplayPattern()
    {
        $formattedTitle = $this->getUserId();
    
        return $formattedTitle;
    }

    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     */
    public function __toString()
    {
        return $this->getId();
    }

    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.sunilb.com/php/php5-oops-tutorial-magic-methods-__clone-method
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     * (4) http://www.pantovic.com/article/26/doctrine2-entity-cloning
     */
    public function __clone()
    {
        // If the entity has an identity, proceed as normal.
        if ($this->id) {
            // create new instance
            
            $entity = new \Cmfcmf\OAuthModule\Entity\MappedIdEntity();
            // unset identifiers
            $entity->setId(null);
            // copy simple fields
            $entity->set_objectType($this->get_objectType());
            $entity->set_actions($this->get_actions());
            $entity->initValidator();
            $entity->setUserId($this->getUserId());
            $entity->setClaimedId($this->getClaimedId());
            $entity->setProvider($this->getProvider());
    
    
            return $entity;
        }
        // otherwise do nothing, do NOT throw an exception!
    }
}
