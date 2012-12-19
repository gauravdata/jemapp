<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Pap_Mail_Reports_WeeklyReport extends Pap_Mail_Reports_Report {

	public function __construct(Pap_Stats_Params $statsParams = null) {
		parent::__construct($statsParams);
		$this->subject = Gpf_Lang::_runtime('Weekly report');
	}

	protected function initTemplate() {
		$this->mailTemplateFile = 'weekly_report.stpl';
		$this->templateName = Gpf_Lang::_runtime('Merchant - Weekly report');
	}

	protected function initDate(Pap_Stats_Params $statsParams, $timeOffset) {
		$clientTime = $this->createCurrentTime() + $timeOffset;
		$lastStartDay = $this->getLastStartDay($clientTime);

		$this->dateFrom = Gpf_DbEngine_Database::getDateString(
		mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - $lastStartDay, date("Y",$clientTime)) - $timeOffset);

		$this->dateTo = Gpf_DbEngine_Database::getDateString(
		mktime(23,59,59,date("m",$clientTime), date("d",$clientTime) - $lastStartDay + 6, date("Y",$clientTime)) - $timeOffset);

		$statsParams->setDateRange($this->dateFrom, $this->dateTo);
	}

	protected function initTemplateVariables() {
		parent::initTemplateVariables();
		$this->addVariable('weekstart', $this->_("Week start"));
		$this->addVariable('weekend', $this->_("Week end"));
	}

	protected function setVariableValues() {
		parent::setVariableValues();
		$this->setVariable('weekstart', $this->getDayName(Gpf_Settings::get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_START_DAY)));
		$this->setVariable('weekend', $this->getDayName(Gpf_Settings::get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_SENT_ON)));
	}

	/**
	 * @throws Gpf_Exception
	 * @param $code
	 * @return String
	 */
	private function getDayName($code) {
		switch ($code) {
			case '0': return $this->_('Sunday');
			case '1': return $this->_('Monday');
			case '2': return $this->_('Tuesday');
			case '3': return $this->_('Wednesday');
			case '4': return $this->_('Thursday');
			case '5': return $this->_('Friday');
			case '6': return $this->_('Saturday');
			default: throw new Gpf_Exception('Unknown day code');
		}
	}

	private function getLastStartDay($time) {
		$today = date('w', $time);
		$days = $today - Gpf_Settings::get(Pap_Settings::NOTIFICATION_WEEKLY_REPORT_START_DAY);
		if ($days >= 0) {
			return $days + 7;
		}
		return 14 + $days;
	}
	
	protected function createCurrentTime() {
	    return time();
	}
}

?>
