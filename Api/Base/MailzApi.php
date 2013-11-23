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

namespace Cmfcmf\OAuthModule\Api\Base;

use ModUtil;
use ServiceUtil;
use Zikula_AbstractApi;
use Zikula_View;

/**
 * Mailz api base class.
 */
class MailzApi extends Zikula_AbstractApi
{
    /**
     * Returns existing Mailz plugins with type / title.
     *
     * @param array $args List of arguments.
     *
     * @return array List of provided plugin functions.
     */
    public function getPlugins(array $args = array())
    {
        $plugins = array();
        $plugins[] = array(
            'pluginid'      => 1,
            'module'        => 'CmfcmfOAuthModule',
            'title'         => $this->__('3 newest users'),
            'description'   => $this->__('A list of the three newest users.')
        );
        $plugins[] = array(
            'pluginid'      => 2,
            'module'        => 'CmfcmfOAuthModule',
            'title'         => $this->__('3 random users'),
            'description'   => $this->__('A list of three random users.')
        );
        return $plugins;
    }
    
    /**
     * Returns the content for a given Mailz plugin.
     *
     * @param array    $args                List of arguments.
     * @param int      $args['pluginid']    id number of plugin (internal id for this module, see getPlugins method).
     * @param string   $args['params']      optional, show specific one or all otherwise.
     * @param int      $args['uid']         optional, user id for user specific content.
     * @param string   $args['contenttype'] h or t for html or text.
     * @param datetime $args['last']        timestamp of last newsletter.
     *
     * @return string output of plugin template.
     */
    public function getContent(array $args = array())
    {
        ModUtil::initOOModule('CmfcmfOAuthModule');
        // $args is something like:
        // Array ( [uid] => 5 [contenttype] => h [pluginid] => 1 [nid] => 1 [last] => 0000-00-00 00:00:00 [params] => Array ( [] => ) ) 1
        $objectType = 'user';
    
        $entityClass = '\\Cmfcmf\\OAuthModule\\Entity\\' . ucwords($objectType) . 'Entity';
        $serviceManager = ServiceUtil::getManager();
        $entityManager = $serviceManager->getService('doctrine.entitymanager');
        $repository = $entityManager->getRepository($entityClass);
    
        $idFields = ModUtil::apiFunc('CmfcmfOAuthModule', 'selection', 'getIdFields', array('ot' => $objectType));
    
        $sortParam = '';
        if ($args['pluginid'] == 2) {
            $sortParam = 'RAND()';
        } elseif ($args['pluginid'] == 1) {
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
        }
    
        $where = ''/*$this->filter*/;
        $resultsPerPage = 3;
    
        // get objects from database
        $selectionArgs = array(
            'ot' => $objectType,
            'where' => $where,
            'orderBy' => $sortParam,
            'currentPage' => 1,
            'resultsPerPage' => $resultsPerPage
        );
        list($entities, $objectCount) = ModUtil::apiFunc('CmfcmfOAuthModule', 'selection', 'getEntitiesPaginated', $selectionArgs);
    
        $view = Zikula_View::getInstance('CmfcmfOAuthModule', true);
    
        //$data = array('sorting' => $this->sorting, 'amount' => $this->amount, 'filter' => $this->filter, 'template' => $this->template);
        //$view->assign('vars', $data);
    
        $view->assign('objectType', $objectType)
             ->assign('items', $entities)
             ->assign($repository->getAdditionalTemplateParameters('api', array('name' => 'mailz')));
    
        if ($args['contenttype'] == 't') { /* text */
            return $view->fetch('Mailz/itemlist_user_text.tpl');
        } else {
            //return $view->fetch('ContentType/itemlist_display.html');
            return $view->fetch('Mailz/itemlist_user_html.tpl');
        }
    }
}