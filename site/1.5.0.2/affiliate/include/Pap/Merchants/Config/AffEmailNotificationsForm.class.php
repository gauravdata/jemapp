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
class Pap_Merchants_Config_AffEmailNotificationsForm extends Pap_Merchants_Config_EmailNotificationsFormBase {

    /**
     * @service affiliate_email_notification read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT));
            
        $form->setField(Pap_Settings::AFF_NOTOFICATION_BEFORE_APPROVAL,
            Gpf_Settings::get(Pap_Settings::AFF_NOTOFICATION_BEFORE_APPROVAL));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_STATUS,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_STATUS));

        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_OPTION_STATUS,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_OPTION_STATUS));
            
       	$form->setField(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME,
       		Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME));

       	$form->setField(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME,
       		Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME));
       		
        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME));
            
       	$form->setField(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME,
       		Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME));

        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_STATUS_FOR_CAMPAIGN,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_STATUS_FOR_CAMPAIGN));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_CAMPAIGN_INVITATION,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_CAMPAIGN_INVITATION));
        
        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME));
            
        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT));

        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME));

        $form->setField(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME));
            
        $form->setField(Pap_Settings::AFF_NOTOFICATION_SIGNUP_APPROVED_DECLINED,
            Gpf_Settings::get(Pap_Settings::AFF_NOTOFICATION_SIGNUP_APPROVED_DECLINED));
            
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Merchants_Config_AffEmailNotificationsForm.load', $form);
            
        return $form;
    }
	
    /**
     * @service affiliate_email_notification write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_STATUS,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_STATUS));

        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_OPTION_STATUS,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_OPTION_STATUS));
                        		  
		$this->saveReportsSettings($form);
            
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED));
               
       	Gpf_Settings::set(Pap_Settings::AFF_NOTOFICATION_BEFORE_APPROVAL,
       	    $this->getFieldValue($form, Pap_Settings::AFF_NOTOFICATION_BEFORE_APPROVAL));
       	    
       	Gpf_Settings::set(Pap_Settings::AFF_NOTOFICATION_SIGNUP_APPROVED_DECLINED,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTOFICATION_SIGNUP_APPROVED_DECLINED));
       	
       	Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME,
       	    $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME));
       	Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME,
       	    $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME));
       	Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME,
       	    $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME));
       	
       	Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME,
       	    $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME));
       	Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME,
       	    $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_DEFAULT_SETTING_NAME));
       	Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME,
       	    $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME));
       	Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT,
       	    $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT));
       	    
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_STATUS_FOR_CAMPAIGN,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_STATUS_FOR_CAMPAIGN));
            
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_CAMPAIGN_INVITATION,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_CAMPAIGN_INVITATION));
        
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME));
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME));
            
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Merchants_Config_AffEmailNotificationsForm.save', $form);
            
       	$form->setInfoMessage($this->_("Email notifications for affiliates saved"));
        return $form;
    }
    
	///////////////////////////////////////////////////////////////////////////////////////////////////////
    //  Next methods will be removed when all accounts will be able own affiliates
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * This method will be removed when all accounts will be able own affiliates
     */
    protected function initAccountId(Gpf_Db_PlannedTask $task) {
    	$task->setAccountId(Gpf_Application::getInstance()->getAccountId());
    }
    
    /**
     * This method will be removed when all accounts will be able own affiliates
     */
    protected function isMailReportOn() {
    	$masterMerchantSettings = Gpf_Settings::getAccountSettings(Gpf_Application::getInstance()->getAccountId());
    	$masterMerchantReports = $masterMerchantSettings->get(Pap_Settings::NOTIFICATION_DAILY_REPORT) == Gpf::YES ||
       		$masterMerchantSettings->get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT) == Gpf::YES ||
       		$masterMerchantSettings->get(Pap_Settings::NOTIFICATION_MONTHLY_REPORT) == Gpf::YES;
       	
    	return $masterMerchantReports || $this->isReportsForAffiliatesEnabled();
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    private function saveReportsSettings(Gpf_Rpc_Form $form) {
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED));
            
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED));
            
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED));
            
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT));
            
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT));
            
        Gpf_Settings::set(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT,
            $this->getFieldValue($form, Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT));
            
       	if ($this->isMailReportOn()) {
            $this->insertTask(self::REPORTS_SEND_CLASS);
            return;
        }
        $this->removeTask(self::REPORTS_SEND_CLASS);
    }
}

?>
