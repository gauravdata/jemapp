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
class PremiumWebCart_Main extends Gpf_Plugins_Handler {

    /**
     * @return GoogleCheckout_Main
     */
    public static function getHandlerInstance() {
        return new PremiumWebCart_Main();
    }
    
    public function initSettings($context) {
        $context->addDbSetting(PremiumWebCart_Config::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, Gpf::YES);
        $context->addDbSetting(PremiumWebCart_Config::MERCHANT_ID, '');
        $context->addDbSetting(PremiumWebCart_Config::API_SIGNATURE, '');
    }
}
?>
