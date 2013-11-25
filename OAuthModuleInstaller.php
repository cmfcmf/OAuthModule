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

namespace Cmfcmf\OAuthModule;

use Cmfcmf\OAuthModule\Base\OAuthModuleInstaller as BaseOAuthModuleInstaller;
use LogUtil;
use ModUtil;
use SecurityUtil;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Installer implementation class.
 */
class OAuthModuleInstaller extends BaseOAuthModuleInstaller
{
    /**
     * Add a permission rule to make it possible for users to delete their associated ids.
     * {@inheritdoc}
     */
    public function install()
    {
        $returnValue = parent::install();

        if ($returnValue === true) {
            try {
                // The default group of a new user.
                $defaultGroup = ModUtil::getVar('ZikulaGroupsModule', 'defaultgroup', false);
                $permissionArgs = array('id'        => $defaultGroup,
                                        'component' => $this->name . ':MappedId:',
                                        'instance'  => '.*',
                                        'level'     => ACCESS_DELETE,
                                        'insseq'    => 1
                );

                $permission = ModUtil::apiFunc('ZikulaPermissionsModule', 'admin', 'create', $permissionArgs);
                $this->setVar('permissionId', $permission->getId());

            } catch (AccessDeniedException $e) {
                // The user seems not to have appropriate access to create the permission rule. Only create a warning.
                LogUtil::registerWarning($this->__("You don't have permission to set a recommended permission rule which is necessary for your users to be able to remove their associated OAuth ids from their accounts."));
            } catch(\RuntimeException $e) {
                return LogUtil::registerError($this->__('An error occured when trying to create the permission scheme.'));
            }
        }

        return $returnValue;
    }

    /**
     * Remove the created permission rule.
     * {@inheritdoc}
     */
    public function uninstall()
    {
        $permissionId = $this->getVar('permissionId');

        $returnValue = parent::uninstall();

        if (is_numeric($permissionId) && $permissionId > 0) {
            try {
                ModUtil::apiFunc('ZikulaPermissionsModule', 'admin', 'delete', array('pid' => $permissionId));
            } catch (AccessDeniedException $e) {
                LogUtil::registerWarning($this->__("You don't have permission to remove the created permission rule."));
            }
        }

        return $returnValue;
    }
}
