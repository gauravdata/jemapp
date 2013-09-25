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
class AlertPay_Main extends Gpf_Plugins_Handler {

    /**
     * @return PayPal_Main
     */
    public static function getHandlerInstance() {
        return new AlertPay_Main();
    }
    
    public function initSettings(Gpf_Settings_Gpf $context) {
        $context->addDbSetting(AlertPay_Config::CUSTOM_FIELD_NUMBER, '1');
        $context->addDbSetting(AlertPay_Config::SECURITY_CODE, '');
        $context->addDbSetting(AlertPay_Config::ALLOW_TEST_SALES, '');
        $context->addDbSetting(AlertPay_Config::DIFF_RECURRING_COMMISSIONS, Gpf::NO);
    }
}
?>
