<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
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
class BusinessCatalyst_Main extends Gpf_Plugins_Handler {

    /**
     * @return BusinessCatalyst_Main
     */
    public static function getHandlerInstance() {
        return new BusinessCatalyst_Main();
    }
    
    public function initSettings($context) {
        $context->addDbSetting(BusinessCatalyst_Config::LOGIN, '');
        $context->addDbSetting(BusinessCatalyst_Config::PASSWORD, '');
        $context->addDbSetting(BusinessCatalyst_Config::SITE_ID, '');
        $context->addDbSetting(BusinessCatalyst_Config::PAP_CUSTOM_FIELD_NAME, '');
        $context->addDbSetting(BusinessCatalyst_Config::BC_DOMAIN_NAME, '');
        $context->addDbSetting(BusinessCatalyst_Config::BC_LAST_CHECK, Gpf_Common_DateUtils::getDateTime(1));
        $context->addDbSetting(BusinessCatalyst_Config::BC_LAST_ENTITY_ID, '0');
    }
}

?>
