<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
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
class SafePay_Main extends Gpf_Plugins_Handler {

    /**
     * @return SafePay_Main
     */
    public static function getHandlerInstance() {
        return new SafePay_Main();
    }
    
    public function initSettings($context) {
        $context->addDbSetting(SafePay_Config::CUSTOM_FIELD_NUMBER, '5');
        $context->addDbSetting(SafePay_Config::SECRET_PASSPHRASE, '');
    }
}
?>
