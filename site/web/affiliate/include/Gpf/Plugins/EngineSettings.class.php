<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Context.class.php 18001 2008-05-13 16:05:33Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Plugins_EngineSettings extends Gpf_Object {
    public $activePlugins = array();
    public $extensionPoints = array();

    public function __construct() {
    }

    public function getActivePlugins() {
        return $this->activePlugins;
    }

    public function isPluginActive($codename) {
        return in_array($codename, $this->activePlugins);
    }

    public function getExtensionPoints() {
        return $this->extensionPoints;
    }

    public function init(array $plugins) {

        $arrDefines = array();
        $arrImplements = array();

        foreach($plugins as $plugin) {
            $this->activePlugins[$plugin->getCodeName()] = $plugin->getCodeName();

            $arrDefines = $this->mergeDefines($arrDefines, $plugin->getDefines());
            $arrImplements = array_merge($arrImplements, $plugin->getImplements());
        }

        $this->extensionPoints = $this->generateExtensionPoints($arrDefines, $arrImplements);
    }

    private function mergeDefines($arr1, $arr2) {
        $arrMerged = $arr1;

        foreach($arr2 as $define) {
            if($this->checkExtensionPointExistsInArray($define->getExtensionPoint(), $arr1)) {
                throw new Gpf_Exception("Extension point '".$define->getExtensionPoint()."' was already defined by another plugin, they cannot have duplicated names!");
            }
            $arrMerged[] = $define;
        }

        return $arrMerged;
    }

    private function checkExtensionPointExistsInArray($extensionPointName, $arr) {
        if(count($arr) == 0) {
            return false;
        }

        foreach($arr as $define) {
            if($define->getExtensionPoint() == $extensionPointName) {
                return true;
            }
        }

        return false;
    }

    private function generateExtensionPoints($arrDefines, $arrImplements) {
        $extensionPoints = array();

        foreach($arrDefines as $define) {
            $extensionPointName = $define->getExtensionPoint();
            $contextClass = $define->getClassName();

            $extensionPoints[$extensionPointName]['context'] = $contextClass;
            $extensionPoints[$extensionPointName]['handlers'] = $this->getHandlersForExtensionPoint($extensionPointName, $arrImplements);
        }

        return $extensionPoints;
    }

    private function getHandlersForExtensionPoint($extensionPointName, $arrImplements) {
        $handlers = array();
        foreach($arrImplements as $implements) {
            if($implements->getExtensionPoint() != $extensionPointName) {
                continue;
            }

            $temp = array();
            $temp['class'] = $implements->getClassName();
            $temp['method'] = $implements->getMethodName();
            $temp['priority'] = $implements->getPriority();

            $handlers[] = $temp;
        }

        usort($handlers, array("Gpf_Plugins_EngineSettings", "compareHandlers"));
        
        return $handlers;
    }

    static function compareHandlers($a, $b) {
        if ($a['priority'] == $b['priority']) {
            return 0;
        }
        return ($a['priority'] > $b['priority']) ? -1 : 1;
    }
}

?>
