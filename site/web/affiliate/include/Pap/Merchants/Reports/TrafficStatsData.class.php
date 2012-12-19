<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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
class Pap_Merchants_Reports_TrafficStatsData extends Pap_Common_Overview_OverviewBase {

	/**
	 *
	 * @service traffic_stats read
	 * @param $data
	 */
	public function load(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);
		$filters = $data->getFilters();
		if ($filters->getSize() == 0 || count($filters->getFilter("datetime")) == 0) {
			throw new Exception($this->_("Filter does not contain date parameters"));
		}
		$statsParameters = new Pap_Stats_Params();
		$statsParameters->initFrom($filters);

		$imps = new Pap_Stats_Impressions($statsParameters);
		$clicks = new Pap_Stats_Clicks($statsParameters);
		$sales = new Pap_Stats_Sales($statsParameters);
		$transactions = new Pap_Stats_Transactions($statsParameters);

		$data->setValue("countImpressions", $imps->getCount()->getAll());
		$data->setValue("countClicks", $clicks->getCount()->getAll());
		$data->setValue("countSales", $sales->getCount()->getAll());
		$data->setValue("sumSales", $sales->getTotalCost()->getAll());
		$data->setValue("sumCommissions", $transactions->getCommission()->getAll());
		
		return $data;
	}
}
?>
