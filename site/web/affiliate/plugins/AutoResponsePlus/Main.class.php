<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
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
 * @package PostAffiliatePro
 */
class AutoResponsePlus_Main extends Gpf_Plugins_Handler {
    
    public static function getHandlerInstance() {
        return new AutoResponsePlus_Main();
    }
    
    public function initSettings($context) {
        $context->addDbSetting(AutoResponsePlus_Config::REMOTE_CONTROL_EMAIL, '');
        $context->addDbSetting(AutoResponsePlus_Config::NAME, '');
        $context->addDbSetting(AutoResponsePlus_Config::PASSWORD, '');
        $context->addDbSetting(AutoResponsePlus_Config::AUTORESPONDER_ADDRESS, '');
        $context->addDbSetting(AutoResponsePlus_Config::HTML, Gpf::NO);
        $context->addDbSetting(AutoResponsePlus_Config::TRACKING_TAB, '');
        $context->addDbSetting(AutoResponsePlus_Config::DROP_RULES, Gpf::NO);
    }
    
    public function sendMail(Pap_Contexts_Signup $context) {        
        $mail = new AutoResponsePlus_Mail();
        $mail->setUser($context->getUserObject());
        $mail->addRecipient(Gpf_Settings::get(AutoResponsePlus_Config::REMOTE_CONTROL_EMAIL));        
        $mail->sendNow();
    }
}
?>
