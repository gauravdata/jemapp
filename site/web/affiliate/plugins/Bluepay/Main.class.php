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
 * @package PostAffiliatePro plugins
 */
class Bluepay_Main extends Gpf_Plugins_Handler {

    /**
     * @return Paymate_Main
     */
    public static function getHandlerInstance() {
        return new Bluepay_Main();
    }
    
    public function initSettings($context) {
        $context->addDbSetting(Bluepay_Config::CUSTOM_SEPARATOR, '||');
        $context->addDbSetting(Bluepay_Config::HTML_COOKIE_VARIABLE, 'ORDER_ID');
    }
}
?>
