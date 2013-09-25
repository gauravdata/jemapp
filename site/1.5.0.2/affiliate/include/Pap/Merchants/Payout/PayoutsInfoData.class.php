<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: PayoutInfoData.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Payout_PayoutsInfoData extends Pap_Common_Overview_OverviewBase {

	/**
	 *
	 * @service pay_affiliate_stats read
	 * @param $data
	 */
	public function load(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);
		$statsParams = new Pap_Stats_Params();
		$statsParams->initFrom($data->getFilters());
		$transactions = new Pap_Stats_Transactions($statsParams);

		$data->setValue("paid", $this->getPaidData($data->getFilters()));
		$data->setValue("unpaidApprovedComm", $transactions->getCommission()->getApproved());
		$data->setValue("unpaidPendingComm", $transactions->getCommission()->getPending());
		$data->setValue("unpaidDeclinedComm", $transactions->getCommission()->getDeclined());

		return $data;
	}

	private function getPaidData(Gpf_Rpc_FilterCollection $filters) {
		$select = new Gpf_SqlBuilder_SelectBuilder();
		$select->select->add("SUM(amount)", "paid");
		$select->from->add(Pap_Db_Table_Payouts::getName(), "p");
		$select->from->addInnerJoin(Pap_Db_Table_PayoutsHistory::getName(), "ph", "p.payouthistoryid = ph.payouthistoryid");
		$filters->addTo($select->where);		
		
		Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere', 
        new Gpf_Common_SelectBuilderCompoundRecord($select, new Gpf_Data_Record(array('columnPrefix'), array('ph'))));
		
		$row = $select->getOneRow();

		return $this->checkNullValue($row->get("paid"));
	}
}

?>
