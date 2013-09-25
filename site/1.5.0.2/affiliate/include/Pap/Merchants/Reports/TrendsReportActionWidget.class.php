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
class Pap_Merchants_Reports_TrendsReportActionWidget extends Pap_Merchants_Reports_TrendsReportWidget {


	private $action;
	private $commTypeId;

	protected function getTemplate() {
		return "trends_report_action";
	}

	private function setActionAndCommissionId(Gpf_Rpc_FilterCollection $filters) {
	    $this->action = $filters->getFilterValue('action');
	    $this->commTypeId = $filters->getFilterValue('commtypeid');
	}

	/**
	 * @service trend_stats read
	 * @param $fields
	 */
	public function load(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);
		$this->statParams = $this->createStatParams($data->getFilters());
		$this->setActionAndCommissionId($data->getFilters());
		$this->fillData($data, $params);
		return $data;
	}

	protected function fillDataToTemplate(Gpf_Templates_Template $tmpl, Gpf_Rpc_Params $params) {
		$stats = array();
		$stats['clicks'] = new Pap_Stats_Clicks($this->statParams);
		$stats['transactionTypes'] = new Pap_Stats_TransactionTypeStats($this->statParams);
		$stats['transactionTypesFirstTier'] = new Pap_Stats_TransactionTypeStatsFirstTier($this->statParams);
		$tmpl->assign('selected', $stats);
		$tmpl->assign('actionFilter', $this->action);
		$tmpl->assign('commtypeidFilter', $this->commTypeId);
		return $tmpl;
	}
}
?>
