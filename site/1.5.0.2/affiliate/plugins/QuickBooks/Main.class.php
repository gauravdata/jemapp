<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
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
class QuickBooks_Main extends Gpf_Plugins_Handler {

    /**
     * @return QuickBooks_Main
     */
    public static function getHandlerInstance() {
        return new QuickBooks_Main();
    }
    
    public function initSettings($context) {
        $context->addDbSetting(QuickBooks_Config::ADD_ACCOUNT, Gpf::YES);
        $context->addDbSetting(QuickBooks_Config::ACCOUNT_NAME, 'Affiliate Program');
        $context->addDbSetting(QuickBooks_Config::ACCOUNT_TYPE, 'BANK');
        $context->addDbSetting(QuickBooks_Config::TRNS_ACCOUNT_TYPE, 'Accounts Payable');
        $context->addDbSetting(QuickBooks_Config::SPL_ACCOUNT_TYPE, 'Affiliate Program');
        $context->addDbSetting(QuickBooks_Config::TRNS_TOPRINT, Gpf::YES);
    }
}
?>
