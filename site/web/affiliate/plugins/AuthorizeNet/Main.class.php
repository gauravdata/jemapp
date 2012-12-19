<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
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
 * @package PostAffiliate
 */
class AuthorizeNet_Main extends Gpf_Plugins_Handler {
    
    /**
     * @return PayPal_Main
     */
    public static function getHandlerInstance() {
        return new AuthorizeNet_Main();
    }

    public function initSettings($context) {
        $context->addDbSetting(AuthorizeNet_Config::PARAM_NAME, 'custom');
        $context->addDbSetting(AuthorizeNet_Config::DISCOUNT_TAX, '');
        $context->addDbSetting(AuthorizeNet_Config::FREIGHT_TAX, '');
        $context->addDbSetting(AuthorizeNet_Config::DUTY_TAX, '');
    }
}
?>
