<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class RoboKassa_Main extends Gpf_Plugins_Handler {
    private static $instance = false;
    /**
     * @return RoboKassa_Main
     */
    
    private function __construct() {
    }
    
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new RoboKassa_Main();
        }
        return self::$instance;
    }
    
    public function initSettings(Gpf_Settings_Gpf $context) {
        $context->addDbSetting(RoboKassa_Config::SECURE_PASS2, '');
    }
}
?>
