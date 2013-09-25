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
class TrialPay_Main extends Gpf_Plugins_Handler {
    /**
     * @return TrialPay_Main
     */
    private static $instance = false;
    
    private function __construct() {
    }

    /**
     * @return TrialPay_Main
     */    
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new TrialPay_Main();
        }
        return self::$instance;
    }
    
    public function initSettings($context) {
        $context->addDbSetting(TrialPay_Config::SEPARATOR, '');
    }
}
?>
