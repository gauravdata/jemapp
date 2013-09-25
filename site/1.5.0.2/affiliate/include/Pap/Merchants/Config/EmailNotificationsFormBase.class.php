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
abstract class Pap_Merchants_Config_EmailNotificationsFormBase extends Pap_Merchants_Config_TaskSettingsFormBase {

	const REPORTS_SEND_CLASS = 'Pap_Mail_Reports_ReportsSendTask';		   
    
    protected function isMailReportOn() {
    	return Gpf_Settings::get(Pap_Settings::NOTIFICATION_DAILY_REPORT) == Gpf::YES || 
       	Gpf_Settings::get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT) == Gpf::YES || 
       	Gpf_Settings::get(Pap_Settings::NOTIFICATION_MONTHLY_REPORT) == Gpf::YES ||
       	$this->isReportsForAffiliatesEnabled();
    }
    
    /**
     * @return boolean
     */
    protected function isReportsForAffiliatesEnabled() {
    	return Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED) == Gpf::YES ||
		Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED) == Gpf::YES ||
		Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED) == Gpf::YES ||
		Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT) == Gpf::YES ||
		Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT) == Gpf::YES || 
		Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT) == Gpf::YES;
    }
	
    /**
     * @return array
     */
    protected function getTaskLoadColumns() {
    	$loadColumns = parent::getTaskLoadColumns();
    	$loadColumns[] = Gpf_Db_Table_PlannedTasks::ACCOUNTID;
    	return $loadColumns; 
    }
    
    protected function initAccountId(Gpf_Db_PlannedTask $task) {
    	$task->setAccountId(Gpf_Session::getInstance()->getAuthUser()->getAccountId());	
    }
 
    protected function hasPrivilege($object, $privilege) {
    	return Gpf_Session::getInstance()->getAuthUser()->hasPrivilege($object, $privilege);
    }
}

?>
