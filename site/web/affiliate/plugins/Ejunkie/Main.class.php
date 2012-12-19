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
class Ejunkie_Main extends Gpf_Plugins_Handler {

    /**
     * @return Ejunkie_Main
     */
    public static function getHandlerInstance() {
        return new Ejunkie_Main();
    }

    public function initSettings($context) {
        $context->addDbSetting(Ejunkie_Config::CUSTOM_SEPARATOR, '');
        $context->addDbSetting(Ejunkie_Config::DISCOUNT_TAX, '');
        $context->addDbSetting(Ejunkie_Config::DISCOUNT_FEE, '');
        $context->addDbSetting(Ejunkie_Config::DISCOUNT_HANDLING, '');
        $context->addDbSetting(Ejunkie_Config::DISCOUNT_SHIPPING, '');
        $context->addDbSetting(Ejunkie_Config::USE_RECURRING_COMMISSION_SETTINGS, '');
        $context->addDbSetting(Ejunkie_Config::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, Gpf::YES);
    }
}
?>
