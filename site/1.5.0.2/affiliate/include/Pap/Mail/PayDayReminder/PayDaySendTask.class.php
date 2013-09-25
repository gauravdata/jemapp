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
class Pap_Mail_PayDayReminder_PayDaySendTask extends Pap_Mail_SendTaskBase {

	/**
	 * @var Gpf_Db_PlannedTask
	 */
	private $plannedTask;

	public function getName() {
		return $this->_('Pay day reminder send');
	}

	protected function execute() {		
		parent::execute();
		$this->loadPlannedTask();
		if ($this->accountSettings->get(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER) == Gpf::YES &&
		$this->isPending('payDayReminderSend', $this->_('Send pay day reminder'))) {
			$this->sendPayDayReminder();
			$this->setDone();
		}
	}

	protected function shouldSendMonthly($monthlyDay) {
		return parent::shouldSendMonthly($monthlyDay) && $this->isRecurrenceMonth();
	}

	private function sendPayDayReminder() {
		if ($this->shouldSendMonthly($this->accountSettings->get(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_DAY_OF_MONTH))) {
			$monthlyReport = new Pap_Mail_PayDayReminder_PayDayReminder();
			$monthlyReport->addRecipient($this->accountSettings->get(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL));
			$monthlyReport->sendNow();
			$this->saveLastSendDate();
		}
	}

	private function loadPlannedTask() {
		$this->plannedTask = new Gpf_Db_PlannedTask();
		$this->plannedTask->setClassName(get_class($this));
		$this->plannedTask->setRecurrencePresetId('A');
		$this->plannedTask->setAccountId($this->task->getAccountId());
		try {
			$this->plannedTask->loadFromData(array(Gpf_Db_Table_PlannedTasks::CLASSNAME, Gpf_Db_Table_PlannedTasks::RECURRENCEPRESETID, Gpf_Db_Table_PlannedTasks::ACCOUNTID));
		} catch (Gpf_Exception $e) {
		}
	}

	private function saveLastSendDate() {
		if ($this->plannedTask->isPrimaryKeyEmpty()) {
			return;
		}
		$dateTime = new Gpf_DateTime();
		$this->plannedTask->set(Gpf_Db_Table_PlannedTasks::PARAMS, $dateTime->toDateTime());
		$this->plannedTask->save();

	}

	/**
	 * @return boolean
	 */
	private function isRecurrenceMonth() {
		if ($this->plannedTask->isPrimaryKeyEmpty()) {
			return false;
		}
		$lastSendDate = $this->plannedTask->getParams();
		if (is_null($lastSendDate) || $lastSendDate == '') {
			return true;
		}
		$dateTime = new Gpf_DateTime();
		if (Gpf_Common_DateUtils::getDifference($lastSendDate, $dateTime->toDateTime(), Gpf_Common_DateUtils::MONTH) > $this->accountSettings->get(Pap_Settings::NOTIFICATION_PAY_DAY_REMINDER_RECURRENCE_MONTH)) {
			return true;			
		}
		return false;
	}
}
