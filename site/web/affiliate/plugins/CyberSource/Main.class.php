<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
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
class CyberSource_Main extends Gpf_Plugins_Handler {

    /**
     * @return PayPal_Main
     */
    public static function getHandlerInstance() {
        return new CyberSource_Main();
    }
    
    public function initSettings($context) {
        $context->addDbSetting(CyberSource_Config::CUSTOM_FIELD_NUMBER, '');
    }
}
?>
