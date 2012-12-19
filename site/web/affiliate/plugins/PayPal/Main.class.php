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
class PayPal_Main extends Gpf_Plugins_Handler {

    /**
     * @return PayPal_Main
     */
    public static function getHandlerInstance() {
        return new PayPal_Main();
    }
    
    public function initSettings($context) {
        $context->addDbSetting(PayPal_Config::CUSTOM_SEPARATOR, '');
        $context->addDbSetting(PayPal_Config::USE_COUPON, '');
        $context->addDbSetting(PayPal_Config::DISCOUNT_FEE, '');
        $context->addDbSetting(PayPal_Config::DISCOUNT_HANDLING, '');
        $context->addDbSetting(PayPal_Config::DISCOUNT_SHIPPING, '');
        $context->addDbSetting(PayPal_Config::DISCOUNT_TAX, '');
        $context->addDbSetting(PayPal_Config::REGISTER_AFFILIATE, '');
        $context->addDbSetting(PayPal_Config::USE_RECURRING_COMMISSION_SETTINGS, '');
        $context->addDbSetting(PayPal_Config::NORMAL_COMMISSION_AS_RECURRING_COMMISSION, '');
        $context->addDbSetting(PayPal_Config::TEST_MODE, '');
        $context->addDbSetting(PayPal_Config::APPROVE_AFFILIATE, '');
        $context->addDbSetting(PayPal_Config::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, Gpf::YES);
    }
}
?>
