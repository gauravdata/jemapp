<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Http.class.php 22444 2008-11-21 14:52:37Z mbebjak $
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
class Gpf_Php {

    /**
     * Check if function is enabled and exists in php
     *
     * @param $functionName
     * @return boolean Returns true if function exists and is enabled
     */
    public static function isFunctionEnabled($functionName) {
        if (function_exists($functionName) && strstr(ini_get("disable_functions"), $functionName) === false) {
            return true;
        }
        return false;
    }
    
    /**
     * Check if extension is loaded
     * 
     * @param $extensionName
     * @return boolean Returns true if extension is loaded
     */
    public static function isExtensionLoaded($extensionName) {
        return extension_loaded($extensionName);
    }

}
?>
