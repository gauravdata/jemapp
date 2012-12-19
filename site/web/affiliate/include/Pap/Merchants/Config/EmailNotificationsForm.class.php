<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: LoggingForm.class.php 18882 2008-06-27 12:15:52Z mfric $
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
class Pap_Merchants_Config_EmailNotificationsForm extends Pap_Merchants_Config_EmailNotificationsFormBase {
	
	const PAY_DAY_REMINDER_SEND_CLASS = 'Pap_Mail_PayDayReminder_PayDaySendTask';
	
    /**
     * @service merch_email_notification read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $form->setField(Pap_Settings::NOTIFICATION_ON_SALE,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_ON_SALE));
        
        $form->setField(Pap_Settings::NOTIFICATION_ON_SALE_STATUS,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_ON_SALE_STATUS));

        if ($this->hasPrivilege(Pap_Privileges::DIRECT_LINK, Gpf_Privileges::P_READ)) {
        	$form->setField(Pap_Settings::NOTIFICATION_NEW_DIRECT_LINK,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_NEW_DIRECT_LINK));
        }

        if ($this->hasPrivilege(Pap_Privileges::AFFILIATE, Gpf_Privileges::P_READ)) {
        	$form->setField(Pap_Settings::NOTIFICATION_NEW_USER_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_NEW_USER_SETTING_NAME));
        }

        if ($this->hasPrivilege(Pap_Privileges::PAY_AFFILIATE, Gpf_Privileges::P_READ)) {
			$form->setField(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER));                        
            
			$form->setField(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_DAY_OF_MONTH,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_DAY_OF_MONTH));

			$form->setField(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_RECURRENCE_MONTH,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_RECURRENCE_MONTH));
        }
                    
        $form->setField(Pap_Settings::NOTIFICATION_DAILY_REPORT,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_DAILY_REPORT));
            
        $form->setField(Pap_Settings::NOTIFICATION_WEEKLY_REPORT,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT));
            
		$form->setField(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_START_DAY,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_START_DAY));

        $form->setField(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_SENT_ON,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_SENT_ON));
            
        $form->setField(Pap_Settings::NOTIFICATION_MONTHLY_REPORT,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_MONTHLY_REPORT));
            
        $form->setField(Pap_Settings::NOTIFICATION_MONTHLY_REPORT_SENT_ON,
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_MONTHLY_REPORT_SENT_ON));
                   		
        $form->setField(Pap_Settings::NOTIFICATION_ON_JOIN_TO_CAMPAIGN, 
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_ON_JOIN_TO_CAMPAIGN));
            
        $form->setField(Pap_Settings::NOTIFICATION_ON_COMMISSION_APPROVED, 
            Gpf_Settings::get(Pap_Settings::NOTIFICATION_ON_COMMISSION_APPROVED));

        $form->setField(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL, 
         	Gpf_Settings::get(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL));
         	
        $form->setField(Pap_Settings::REPORTS_MAX_TRANSACTIONS_COUNT, 
            Gpf_Settings::get(Pap_Settings::REPORTS_MAX_TRANSACTIONS_COUNT));
            
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.EmailNotificationsForm.load', $form);

        return $form;
    }
	
    /**
     * @service merch_email_notification write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
                
       	Gpf_Settings::set(Pap_Settings::NOTIFICATION_ON_SALE, 
       		$this->getFieldValue($form, Pap_Settings::NOTIFICATION_ON_SALE));
       		
       	if ($this->hasPrivilege(Pap_Privileges::AFFILIATE, Gpf_Privileges::P_READ)) {        	                
        	Gpf_Settings::set(Pap_Settings::NOTIFICATION_NEW_USER_SETTING_NAME, 
        	$this->getFieldValue($form, Pap_Settings::NOTIFICATION_NEW_USER_SETTING_NAME));
       	}        
        
        Gpf_Settings::set(Pap_Settings::NOTIFICATION_ON_SALE_STATUS,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_ON_SALE_STATUS));
            
        if ($this->hasPrivilege(Pap_Privileges::DIRECT_LINK, Gpf_Privileges::P_READ)) {
        	Gpf_Settings::set(Pap_Settings::NOTIFICATION_NEW_DIRECT_LINK,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_NEW_DIRECT_LINK));
        }

        if ($this->hasPrivilege(Pap_Privileges::PAY_AFFILIATE, Gpf_Privileges::P_READ)) {
			$this->savePayDayReminderSettings($form);            
        }
		$this->saveReportsSettings($form);
             
       	Gpf_Settings::set(Pap_Settings::NOTIFICATION_ON_JOIN_TO_CAMPAIGN, 
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_ON_JOIN_TO_CAMPAIGN));
        Gpf_Settings::set(Pap_Settings::NOTIFICATION_ON_COMMISSION_APPROVED, 
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_ON_COMMISSION_APPROVED));
        
        Gpf_Settings::set(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL,
            $this->getFieldValue($form, Pap_Settings::MERCHANT_NOTIFICATION_EMAIL));
            
        Gpf_Settings::set(Pap_Settings::REPORTS_MAX_TRANSACTIONS_COUNT,
            $this->getFieldValue($form, Pap_Settings::REPORTS_MAX_TRANSACTIONS_COUNT));
            
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.EmailNotificationsForm.save', $form);
        
       	$form->setInfoMessage($this->_("Email notifications saved"));
        return $form;
    }   
    
   	///////////////////////////////////////////////////////////////////////////////////////////////////////    
    //  Next method will be removed when all accounts will be able own affiliates
    ///////////////////////////////////////////////////////////////////////////////////////////////////////    
    
    /**
     * This method will be removed when all accounts will be able own affiliates
     */
    protected function isMailReportOn() {
    	$isReportOn = Gpf_Settings::get(Pap_Settings::NOTIFICATION_DAILY_REPORT) == Gpf::YES || 
       		Gpf_Settings::get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT) == Gpf::YES || 
       		Gpf_Settings::get(Pap_Settings::NOTIFICATION_MONTHLY_REPORT) == Gpf::YES;
    	if (Gpf_Session::getAuthUser()->isMasterMerchant()) {
    		$isReportOn =  $isReportOn || $this->isReportsForAffiliatesEnabled();
    	}    	    	
    	return $isReportOn;       	
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    private function savePayDayReminderSettings(Gpf_Rpc_Form $form) {
		Gpf_Settings::set(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER));

        Gpf_Settings::set(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_DAY_OF_MONTH,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_DAY_OF_MONTH));
            
        Gpf_Settings::set(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_RECURRENCE_MONTH,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_RECURRENCE_MONTH));
            
		if (Gpf_Settings::get(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER) == Gpf::YES) {
            $this->insertTask(self::PAY_DAY_REMINDER_SEND_CLASS);
            return;
        }
        $this->removeTask(self::PAY_DAY_REMINDER_SEND_CLASS);
    }
    
    private function saveReportsSettings(Gpf_Rpc_Form $form) {
    	Gpf_Settings::set(Pap_Settings::NOTIFICATION_DAILY_REPORT,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_DAILY_REPORT));
            
        Gpf_Settings::set(Pap_Settings::NOTIFICATION_WEEKLY_REPORT,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_WEEKLY_REPORT));
            
        Gpf_Settings::set(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_START_DAY,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_WEEKLY_REPORT_START_DAY));
            
        Gpf_Settings::set(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_SENT_ON,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_WEEKLY_REPORT_SENT_ON));
            
        Gpf_Settings::set(Pap_Settings::NOTIFICATION_MONTHLY_REPORT,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_MONTHLY_REPORT));
            
        Gpf_Settings::set(Pap_Settings::NOTIFICATION_MONTHLY_REPORT_SENT_ON,
            $this->getFieldValue($form, Pap_Settings::NOTIFICATION_MONTHLY_REPORT_SENT_ON));
       
       	if ($this->isMailReportOn()) {
            $this->insertTask(self::REPORTS_SEND_CLASS);
            return;
        }
        $this->removeTask(self::REPORTS_SEND_CLASS);
    }
}

?>
