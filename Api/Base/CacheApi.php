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

use Cmfcmf\OAuthModule\Util\ControllerUtil;

use ModUtil;
use Zikula_AbstractApi;
use Zikula_View;
use Zikula_View_Theme;
/**
 * Cache api base class.
 */
class CacheApi extends Zikula_AbstractApi
{
    /**
     * Clear cache for given item. Can be called from other modules to clear an item cache.
     *
     * @param $args['ot']   the treated object type
     * @param $args['item'] the actual object
     */
    public function clearItemCache(array $args = array())
    {
        if (!isset($args['ot']) || !isset($args['item'])) {
            return;
        }
    
        $objectType = $args['ot'];
        $item = $args['item'];
    
        $controllerHelper = new ControllerUtil($this->serviceManager, ModUtil::getModule($this->name));
        $utilArgs = array('api' => 'cache', 'action' => 'clearItemCache');
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            return;
        }
    
        if ($item && !is_array($item) && !is_object($item)) {
            $item = ModUtil::apiFunc($this->name, 'selection', 'getEntity', array('ot' => $objectType, 'id' => $item, 'useJoins' => false, 'slimMode' => true));
        }
    
        if (!$item) {
            return;
        }
    
    
        // Clear View_cache
        $cacheIds = array();
        $cacheIds[] = 'index';
        $cacheIds[] = 'view';
        
        
    
        $view = Zikula_View::getInstance('CmfcmfOAuthModule');
        foreach ($cacheIds as $cacheId) {
            $view->clear_cache(null, $cacheId);
        }
    
    
        // Clear Theme_cache
        $cacheIds = array();
        $cacheIds[] = 'homepage'; // for homepage (can be assigned in the Settings module)
        $cacheIds[] = 'CmfcmfOAuthModule/user/index'; // index function
        $cacheIds[] = 'CmfcmfOAuthModule/user/view/' . $objectType; // view function (list views)
        
        
        $theme = Zikula_View_Theme::getInstance();
        $theme->clear_cacheid_allthemes($cacheIds);
    }
}
