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
class WebMoney_Main extends Gpf_Plugins_Handler {
    private static $instance = false;
    /**
     * @return WebMoney_Main
     */
    
    private function __construct() {
    }
    
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new WebMoney_Main();
        }
        return self::$instance;
    }
    
    public function initSettings(Gpf_Settings_Gpf $context) {
        $context->addDbSetting(WebMoney_Config::SECRET_KEY, '');
        $context->addDbSetting(WebMoney_Config::ALLOW_TEST_SALES, '');
    }
}
?>
