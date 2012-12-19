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
class Pap_Mail_Reports_MonthlyReport extends Pap_Mail_Reports_Report {

	public function __construct(Pap_Stats_Params $statsParams = null) {
		parent::__construct($statsParams);
		$this->subject = Gpf_Lang::_runtime('Monthly report');
	}
	
	protected function initTemplate() {
		$this->mailTemplateFile = 'monthly_report.stpl';
		$this->templateName = Gpf_Lang::_runtime('Merchant - Monthly report');
	}

	protected function initDate(Pap_Stats_Params $statsParams, $timeOffset) {
		$filter = new Gpf_SqlBuilder_Filter(array("month", "DP", "LM"));
		$filter->setTimeOffset($timeOffset);
		$datePreset = $filter->addDateValueToArray(array());
		$statsParams->setDateRange($datePreset["dateFrom"], $datePreset["dateTo"]);
		
		$this->dateFrom = $datePreset["dateFrom"];
		$this->dateTo = $datePreset["dateTo"];
	}
}
