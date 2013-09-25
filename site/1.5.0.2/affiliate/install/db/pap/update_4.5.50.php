<?php
class pap_update_4_5_50 {

	public function execute() {
		$masterAccountSettings = Gpf_Settings::getAccountSettings(Gpf_Application::getInstance()->getAccountId());
			
		if ($masterAccountSettings->get(Pap_settings::NOTIFICATION_DAILY_REPORT) ||
		$masterAccountSettings->get(Pap_settings::NOTIFICATION_WEEKLY_REPORT) ||
		$masterAccountSettings->get(Pap_settings::NOTIFICATION_MONTHLY_REPORT) ||
		Gpf_Settings::get(Pap_settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED) ||
		Gpf_Settings::get(Pap_settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT) ||
		Gpf_Settings::get(Pap_settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED) ||
		Gpf_Settings::get(Pap_settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT) ||
		Gpf_Settings::get(Pap_settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED) ||
		Gpf_Settings::get(Pap_settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT)) {
			$this->saveReportsSendTask();
		}
	}

	private function saveReportsSendTask() {
		$task = new Gpf_Db_PlannedTask();
		$task->setClassName(Pap_Merchants_Config_EmailNotificationsFormBase::REPORTS_SEND_CLASS);
		$task->setRecurrencePresetId('A');
		$task->setParams(serialize(array('lastdate' => Gpf_Common_DateUtils::now())));
		$task->setAccountId(Gpf_Application::getInstance()->getAccountId());
		try {
			$task->loadFromData(array(
				Gpf_Db_Table_PlannedTasks::CLASSNAME, 
				Gpf_Db_Table_PlannedTasks::RECURRENCEPRESETID,
				Gpf_Db_Table_PlannedTasks::ACCOUNTID));
		} catch (Gpf_DbEngine_NoRowException $e) {
			$task->insert();
		} catch (Gpf_DbEngine_TooManyRowsException $e) {
		}
	}
}
?>
