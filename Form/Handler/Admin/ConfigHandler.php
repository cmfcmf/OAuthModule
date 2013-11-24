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

namespace Cmfcmf\OAuthModule\Form\Handler\Admin;

use Cmfcmf\OAuthModule\Form\Handler\Admin\Base\ConfigHandler as BaseConfigHandler;
use Cmfcmf\OAuthModule\Util\ProviderUtil;
use LogUtil;
use ModUtil;
use SecurityUtil;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zikula_Form_View;

/**
 * Configuration handler implementation class.
 */
class ConfigHandler extends BaseConfigHandler
{
    /**
     * Initialize form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param Zikula_Form_View $view The form view instance.
     *
     * @return boolean False in case of initialization errors, otherwise true.
     *
     * @throws AccessDeniedHttpException Thrown if the user doesn't have admin permissions
     */
    public function initialize(Zikula_Form_View $view)
    {
        $providers = ProviderUtil::getAllOAuthProvider();
        $view->assign('providers', $providers);
        $view->assign('autocompleteAttribute', array('autocomplete' => 'off'));
        return parent::initialize($view);
    }

    /**
     * Command event handler.
     *
     * This event handler is called when a command is issued by the user. Commands are typically something
     * that originates from a {@link Zikula_Form_Plugin_Button} plugin. The passed args contains different properties
     * depending on the command source, but you should at least find a <var>$args['commandName']</var>
     * value indicating the name of the command. The command name is normally specified by the plugin
     * that initiated the command.
     *
     * @param Zikula_Form_View $view The form view instance.
     * @param array            $args Additional arguments.
     *
     * @see Zikula_Form_Plugin_Button
     * @see Zikula_Form_Plugin_ImageButton
     *
     * @return mixed Redirect or false on errors.
     */
    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'save') {
            // check if all fields are valid
            if (!$this->view->isValid()) {
                return false;
            }

            // retrieve form data
            $data = $this->view->getValues();

            foreach ($data['config'] as $name => $value) {
                if (!strncmp($name, 'registrationProvider', strlen('registrationProvider'))) {
                    if ($value === true) {
                        $provider = substr($name, strlen('registrationProvider'));
                        $data['config'][('loginProvider' . $provider)] = true;
                    }
                }
            }

            // update all module vars
            if (!$this->setVars($data['config'])) {
                throw new \RuntimeException($this->__('Error! Failed to set configuration variables.'));
            }

            LogUtil::registerStatus($this->__('Done! Module configuration updated.'));
        } else if ($args['commandName'] == 'cancel') {
            // nothing to do there
        }

        // redirect back to the config page
        $url = ModUtil::url($this->name, 'admin', 'config');

        return $this->view->redirect($url);
    }
}
