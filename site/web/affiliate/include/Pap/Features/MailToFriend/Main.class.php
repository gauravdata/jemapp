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
class Pap_Features_MailToFriend_Main extends Gpf_Plugins_Handler {
    
	/**
	 * @return Pap_Features_MailToFriend_Main
	 */
    public static function getHandlerInstance() {
        return new Pap_Features_MailToFriend_Main();
    }

    public function loadSetting(Gpf_Rpc_Form $form) {
        $form->setField(Pap_Settings::AFF_SEND_EMAILS_PER_MINUTE_SETTING_NAME,Gpf_Settings::get(Pap_Settings::AFF_SEND_EMAILS_PER_MINUTE_SETTING_NAME));
        return $form;
    }
    
    public function saveSetting(Gpf_Rpc_Form $form) {
        Gpf_Settings::set(Pap_Settings::AFF_SEND_EMAILS_PER_MINUTE_SETTING_NAME, $form->getFieldValue(Pap_Settings::AFF_SEND_EMAILS_PER_MINUTE_SETTING_NAME));
    }
    
    public function raiseScheduledTime(Gpf_Plugins_ValueContext $valueContext) {
        $outboxArray = $valueContext->getArray();
        $outbox = $outboxArray[0];
        $outbox->set('scheduled_at', strftime("%Y-%m-%d %H:%M:%S", time() + 60 * $valueContext->get()));
    }
    
    public function getUserFieldPersonalMessage($valueContext) {
        $userFields = $valueContext->getArray();
        $userFields['personalmessage'] = $this->_('Personal message');
    }

}
?>
