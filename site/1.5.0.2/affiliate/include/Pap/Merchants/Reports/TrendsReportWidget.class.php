<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author MichalBebjak
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Reports_TrendsReportWidget extends Pap_Common_ServerTemplatePanel {

	/**
	 * @var Pap_Stats_Params
	 */
	protected $statParams;

	protected function getTemplate() {
		return "trends_report";
	}

	/**
	 * @return Pap_Stats_Params
	 */
	protected function createStatParams(Gpf_Rpc_FilterCollection $filters) {
		$statParams = new Pap_Stats_Params();
		$statParams->initFrom($filters);
		if (!$statParams->isStatusDefined()) {
			$statParams->setStatus(Pap_Common_Constants::STATUS_APPROVED);
		}
		$statParams->setDateFrom(new Gpf_DateTime(0));
		$statParams->setDateTo(new Gpf_DateTime());
		$dateFilter = $filters->getFilter("datetime");
		if (sizeof($dateFilter) > 0) {
			$this->setDateFilter($statParams, $dateFilter);
		}
		return $statParams;
	}

	private function setDateFilter(Pap_Stats_Params $statParams, array $dateFilter) {
		$datePreset = array();
		foreach ($dateFilter as $filter) {
			$datePreset = $filter->addDateValueToArray($datePreset);
		}
		$statParams->setDateRange($datePreset["dateFrom"], $datePreset["dateTo"]);
	}	

	/**
	 * @service trend_stats read
	 * @param $fields
	 */
	public function load(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);
		$this->statParams = $this->createStatParams($data->getFilters());
		$this->fillData($data, $params);
		return $data;
	}

	protected function fillDataToTemplate(Gpf_Templates_Template $tmpl, Gpf_Rpc_Params $params) {
		$stats = array();
		$stats['clicks'] = new Pap_Stats_Clicks($this->statParams);
		$stats['impressions'] = new Pap_Stats_Impressions($this->statParams);
		$stats['transactionTypes'] = new Pap_Stats_TransactionTypeStats($this->statParams);
		$stats['transactionTypesFirstTier'] = new Pap_Stats_TransactionTypeStatsFirstTier($this->statParams);
		$stats['transactions'] = new Pap_Stats_Transactions($this->statParams);
		$stats['transactionsFirstTier'] = new Pap_Stats_TransactionsFirstTier($this->statParams);
		$stats['transactionsHigherTier'] = new Pap_Stats_TransactionsHigherTiers($this->statParams);
		$tmpl->assign('selected', $stats);
		return $tmpl;
	}
}
?>
