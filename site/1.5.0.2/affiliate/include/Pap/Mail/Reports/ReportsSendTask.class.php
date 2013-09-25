<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Mail_Reports_ReportsSendTask extends Pap_Mail_SendTaskBase {

    public function getName() {
        return $this->_('Reports send');
    }

    protected function execute() {
        parent::execute();
        $dateFrom = $this->getDateFromParams($this->getParams());

		$this->processMerchantReports($dateFrom);
        if ($this->hasAffiliateAnyReportOn()) {
            $this->processAffiliatesReports($dateFrom);
        }
        
        $this->updatePlannedTaskParams();
    }

    private function updatePlannedTaskParams() {
        $plannedTask = new Gpf_Db_PlannedTask();
        $plannedTask->setClassName(Pap_Merchants_Config_EmailNotificationsFormBase::REPORTS_SEND_CLASS);
        $plannedTask->setAccountId($this->task->getAccountId());
        try {
            $plannedTask->loadFromData(array(Gpf_Db_Table_PlannedTasks::CLASSNAME, Gpf_Db_Table_PlannedTasks::ACCOUNTID));
            $plannedTask->setParams($this->getSerializedDateParams(Gpf_Common_DateUtils::getDate($this->time)));
            $plannedTask->setLastPlanDate(Gpf_Common_DateUtils::addDateUnit(Gpf_Common_DateUtils::getDate($this->time), 1, Gpf_Common_DateUtils::DAY));
            $plannedTask->save();
        } catch (Gpf_Exception $e) {
            Gpf_Log::error('Error during updating planned ReportsSendTask: ' . $e->getMessage());
        }
    }


    private function getSerializedDateParams($date) {
        return serialize(array('lastdate' => $date));
    }


    private function getDateFromParams($dateParams) {
        $params = unserialize($dateParams);
        return $params['lastdate'];
    }

    private function processMerchantReports($dateFrom) {
        if ($this->accountSettings->get(Pap_Settings::NOTIFICATION_DAILY_REPORT) == Gpf::YES &&
        $this->isPending('dailyReportSend', $this->_('Send daily report'))) {
            $this->sendReportToMerchant(new Pap_Mail_Reports_DailyReport(new Pap_Stats_Params(new Gpf_DateTime($this->time))));
            $this->setDone();
        }
        if ($this->accountSettings->get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT) == Gpf::YES &&
        $this->isPending('weeklyReportSend', $this->_('Send weekly report'))) {
            $this->sendWeeklyReport($dateFrom);
            $this->setDone();
        }
        if ($this->accountSettings->get(Pap_Settings::NOTIFICATION_MONTHLY_REPORT) == Gpf::YES &&
        $this->isPending('monthlyReportSend', $this->_('Send monthly report'))) {
            $this->sendMonthlyReport($dateFrom);
            $this->setDone();
        }
    }

    private function sendReportToMerchant(Pap_Mail_Reports_Report $merchantReport) {
        $merchantReport->addRecipient($this->accountSettings->get(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL));
        $merchantReport->setAccountId($this->task->getAccountId());
        $merchantReport->send();
    }

    private function wasCronStopped($dateFrom) {
        $correctDate = Gpf_Common_DateUtils::addDateUnitToTimestamp($this->time, -1, Gpf_Common_DateUtils::DAY);

        if (Gpf_Common_DateUtils::getOnlyDatePart($correctDate) == Gpf_Common_DateUtils::getOnlyDatePart($dateFrom)) {
            return false;
        }
        return true;
    }

    private function sendWeeklyReport($dateFrom) {
        if ($this->isWeeklySendDay($dateFrom)) {
            $this->sendReportToMerchant(new Pap_Mail_Reports_WeeklyReport(new Pap_Stats_Params()));
        }
    }

    private function sendMonthlyReport($dateFrom) {
        if ($this->shouldSendMonthly($this->accountSettings->get(Pap_Settings::NOTIFICATION_MONTHLY_REPORT_SENT_ON), $dateFrom)) {
            $this->sendReportToMerchant(new Pap_Mail_Reports_MonthlyReport(new Pap_Stats_Params()));
        }
    }

    private function interruptIfMemoryFull() {
        if ($this->checkIfMemoryIsFull(memory_get_usage())) {
            Gpf_Log::warning('Be carefull, memory was filled up so im interrupting Pap_Mail_Reports_ReportsSendTask task.');
            $this->setDone();
            $this->interrupt();
        }
    }

    private function processAffiliatesReports($dateFrom) {
        foreach ($this->getAccountAffiliatesID()->getAllRowsIterator() as $row) {
            $this->interruptIfMemoryFull();
            try {
                $affiliate = Pap_Affiliates_User::getUserById($row->get(Pap_Db_Table_Users::ID));
                Gpf_Db_Table_UserAttributes::getInstance()->loadAttributes($affiliate->getAccountUserId());
            } catch (Gpf_DbEngine_NoRowException $e) {
                $this->setDone();
                continue;
            }

            if ($this->isAffDailyReportEnabled() && $this->isNotificationPending('affDailyReport', 'Send affiliates daily report', $affiliate) &&
            $this->hasNotifyEnabled('aff_notification_daily_report', Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED, Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT)) {
                $this->sendAffReport(new Pap_Mail_Reports_AffDailyReport(new Pap_Stats_Params(new Gpf_DateTime($this->time))), $affiliate);
            }
            $this->setDone();
            $this->interruptIfMemoryFull();

            if ($this->isAffWeeklyReportEnabled() && $this->isNotificationPending('affWeeklyReport', 'Send affiliates weekly report', $affiliate) &&
            $this->hasNotifyEnabled('aff_notification_weekly_report', Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED, Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT)) {
                $this->sendAffWeeklyReport($affiliate, $dateFrom);
            }
            $this->setDone();
            $this->interruptIfMemoryFull();

            if ($this->isAffMonthlyReportEnabled() && $this->isNotificationPending('affMonthlyReport', 'Send affiliates monthly report', $affiliate) &&
            $this->hasNotifyEnabled('aff_notification_monthly_report', Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED, Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT)) {
                $this->sendAffMonthlyReport($affiliate, $dateFrom);
            }
            $this->setDone();
        }
    }

    /**
	 * @return boolean
     */
    private function isNotificationPending($prefixCode, $translateMessage, $affiliate) {
    	return $this->isPending($prefixCode.$affiliate->getId(), $this->_($translateMessage));
    }
    
    /**
     * @return boolean
     */
    private function hasAffiliateAnyReportOn() {
        return $this->isAffDailyReportEnabled() || $this->isAffWeeklyReportEnabled() || $this->isAffMonthlyReportEnabled();
    }

    /**
     * @param String $affAttribute
     * @param String $changeableByAffSettingName
     * @param String $defaultValueSettingName
     * @return boolean
     */
    private function hasNotifyEnabled($affAttribute, $changeableByAffSettingName, $defaultValueSettingName) {    	
    	if ($this->accountSettings->get($changeableByAffSettingName) == Gpf::YES) {
            $isNotify = Gpf_Db_Table_UserAttributes::getInstance()->getAttributeWithDefaultValue($affAttribute, $this->accountSettings->get($defaultValueSettingName));
		} else {
			$isNotify = $this->accountSettings->get($defaultValueSettingName);
		}    	
        return $isNotify === Gpf::YES;
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    private function getAccountAffiliatesID() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('pu.'.Pap_Db_Table_Users::ID, Pap_Db_Table_Users::ID);
        $select->from->add(Pap_Db_Table_Users::getName(), 'pu');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'qu',
		'pu.'.Pap_Db_Table_Users::ACCOUNTUSERID.'=qu.'.Gpf_Db_Table_Users::ID);
        $select->where->add('pu.'.Pap_Db_Table_Users::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
        $select->where->add('pu.'.Pap_Db_Table_Users::DELETED, '=', 'N');
        $select->where->add('qu.'.Gpf_Db_Table_Users::STATUS, '=', Pap_Common_Constants::STATUS_APPROVED);
        $select->where->add('qu.'.Gpf_Db_Table_Users::ACCOUNTID, '=', $this->task->getAccountId());
        return $select;
    }

    private function sendAffReport(Pap_Mail_Reports_Report $report, Pap_Common_User $affiliate) {
        $report->setUser($affiliate);
        $report->addRecipient($affiliate->getEmail());
        $report->sendNow();
    }

    private function sendAffWeeklyReport(Pap_Common_User $affiliate, $dateFrom) {
        if (!$this->isWeeklySendDay($dateFrom)) {
            return;
        }
        $this->sendAffReport(new Pap_Mail_Reports_AffWeeklyReport(new Pap_Stats_Params()), $affiliate);
    }

    private function sendAffMonthlyReport(Pap_Common_User $affiliate, $dateFrom) {
        if (!$this->shouldSendMonthly($this->accountSettings->get(Pap_Settings::NOTIFICATION_MONTHLY_REPORT_SENT_ON), $dateFrom)) {
            return;
        }
        $this->sendAffReport(new Pap_Mail_Reports_AffMonthlyReport(new Pap_Stats_Params()), $affiliate);
    }

    protected function shouldSendMonthly($monthlyDay, $dateFrom) {
        if (!$this->wasCronStopped($dateFrom)) {
            return parent::shouldSendMonthly($monthlyDay);
        }

        if ($this->isLastDay() && !$this->hasSentDay($monthlyDay)) {
            return true;
        }

        $isThisMonth = date('m', $this->time) == date('m', Gpf_Common_DateUtils::getTimestamp($dateFrom));
        if (!$isThisMonth && date('m', $this->time)-1 != date('m', Gpf_Common_DateUtils::getTimestamp($dateFrom))) {
            return false;
        }

        if ($isThisMonth && date('j', Gpf_Common_DateUtils::getTimestamp($dateFrom)) >= $monthlyDay) {
            return false;
        }

        if (date('j', $this->time) < $monthlyDay) {
            return false;
        }

        return true;
    }

    /**
     * @return boolean
     */
    protected function isWeeklySendDay($dateFrom) {
        $weeklyDay = $this->accountSettings->get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_SENT_ON);

        if (!$this->wasCronStopped($dateFrom)) {
            return date('w', $this->time) == $weeklyDay;
        }

        $dateFromTimestamp = Gpf_Common_DateUtils::getTimestamp($dateFrom);

        $dateStart = new Gpf_DateTime($dateFromTimestamp);
        $dateStart->addWeek(-1);
        $dateEnd = new Gpf_DateTime($dateFromTimestamp);
        $lastWeek = Gpf_Common_DateUtils::addDateUnitToTimestamp($this->time, -1, Gpf_Common_DateUtils::WEEK);

        if ($lastWeek < $dateStart->getWeekStart()->toDateTime() ||
        $lastWeek > $dateEnd->getWeekEnd()->toDateTime()) {
            return false;
        }

        if ($lastWeek >= $dateStart->getWeekStart()->toDateTime() &&
        $lastWeek <= $dateStart->getWeekEnd()->toDateTime() &&
        date('w', $dateFromTimestamp) >= $weeklyDay) {
            return false;
        }

        if (date('w', $this->time) < $weeklyDay) {
            return false;
        }
        return true;
    }

    /**
     * @return boolean
     */
    private function isAffDailyReportEnabled() {
        return $this->accountSettings->get(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_ENABLED) == Gpf::YES ||
        $this->accountSettings->get(Pap_Settings::AFF_NOTIFICATION_DAILY_REPORT_DEFAULT) == Gpf::YES;
    }

    /**
     * @return boolean
     */
    private function isAffWeeklyReportEnabled() {
        return $this->accountSettings->get(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_ENABLED) == Gpf::YES ||
        $this->accountSettings->get(Pap_Settings::AFF_NOTIFICATION_WEEKLY_REPORT_DEFAULT) == Gpf::YES;
    }

    /**
     * @return boolean
     */
    private function isAffMonthlyReportEnabled() {
        return $this->accountSettings->get(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_ENABLED) == Gpf::YES ||
        $this->accountSettings->get(Pap_Settings::AFF_NOTIFICATION_MONTHLY_REPORT_DEFAULT) == Gpf::YES;
    }
}

?>
