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

/**
 * Bootstrap called when application is first initialised at runtime.
 *
 * This is only called once, and only if the core has reason to initialise this module,
 * usually to dispatch a controller request or API.
 *
 * For example you can register additional AutoLoaders with ZLoader::addAutoloader($namespace, $path)
 * whereby $namespace is the first part of the PEAR class name
 * and $path is the path to the containing folder.
 */

include_once 'Base/bootstrap.php';

require_once(__DIR__ . '/vendor/autoload.php');