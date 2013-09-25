<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsInfoData.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Transaction_TransactionsInfoData extends Pap_Common_Overview_OverviewBase {

	/**
	 *
	 * @service transaction_stats read
	 * @param $data
	 */
	public function load(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);
		$statsParams = new Pap_Stats_Params();
		$statsParams->initFrom($data->getFilters());
		$transactions = new Pap_Stats_Transactions($statsParams);

		$data->setValue("totalCommisions", $transactions->getCommission()->getAll());
		$data->setValue("totalSales", $transactions->getTotalCost()->getAll());
		$data->setValue("totalPendingCommisions", $transactions->getCommission()->getPending());
		$data->setValue("totalUnpaidApprovedComm", $transactions->getCommission()->getApproved());
		$data->setValue("countPendingCommissions", $transactions->getCount()->getPending());
		$data->setValue("countUnpaidApprovedComm", $transactions->getCount()->getApproved());

		return $data;
	}

	/**
	 * Load transaction detail for transaction manager
	 *
	 * @service transaction_stats read
	 * @param $fields
	 */
	public function transactionDetails(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);

		$ids = $data->getFilters()->getFilter("id");
		 
		if (sizeof($ids) == 1) {
			$id = $ids[0]->getValue();
		}

		$trans = $this->getTransactionData($id);
		if($trans == null) {
			return $data;
		}

		foreach ($trans as $name => $value) {
			$data->setValue($name, $value);
		}

		return $data;
	}

	/**
	 * @throws Gpf_DbEngine_TooManyRowsException
	 * @throws Gpf_DbEngine_NoRowException
	 * @param string $transId
	 * @return Gpf_Data_Record
	 */
	private function getTransactionData($transId) {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();

		$selectBuilder->select->add(Pap_Db_Table_Transactions::TRANSACTION_ID, Pap_Db_Table_Transactions::TRANSACTION_ID, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::USER_ID, Pap_Db_Table_Transactions::USER_ID, 't');
		$selectBuilder->select->add(Gpf_Db_Table_AuthUsers::USERNAME, Gpf_Db_Table_AuthUsers::USERNAME, 'au');
		$selectBuilder->select->add(Gpf_Db_Table_AuthUsers::FIRSTNAME, Gpf_Db_Table_AuthUsers::FIRSTNAME, 'au');
		$selectBuilder->select->add(Gpf_Db_Table_AuthUsers::LASTNAME, Gpf_Db_Table_AuthUsers::LASTNAME, 'au');
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::SYMBOL, Gpf_Db_Table_Currencies::SYMBOL, "c");
		$selectBuilder->select->add(Gpf_Db_Table_Currencies::WHEREDISPLAY, Gpf_Db_Table_Currencies::WHEREDISPLAY, "c");
		$selectBuilder->select->add(Pap_Db_Table_Campaigns::NAME,  "campaignname", "ca");
		$selectBuilder->select->add(Pap_Db_Table_Transactions::R_STATUS, Pap_Db_Table_Transactions::R_STATUS, "t");
		$selectBuilder->select->add(Pap_Db_Table_Transactions::R_TYPE, Pap_Db_Table_Transactions::R_TYPE, "t");
		$selectBuilder->select->add(Pap_Db_Table_Transactions::DATE_INSERTED, Pap_Db_Table_Transactions::DATE_INSERTED, "t");
		$selectBuilder->select->add(Pap_Db_Table_Transactions::DATE_APPROVED, Pap_Db_Table_Transactions::DATE_APPROVED, "t");
		$selectBuilder->select->add(Pap_Db_Table_Transactions::PAYOUT_STATUS, Pap_Db_Table_Transactions::PAYOUT_STATUS, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::REFERER_URL, Pap_Db_Table_Transactions::REFERER_URL, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::IP, Pap_Db_Table_Transactions::IP, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::COMMISSION, Pap_Db_Table_Transactions::COMMISSION, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::TIER, Pap_Db_Table_Transactions::TIER, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::ORDER_ID, Pap_Db_Table_Transactions::ORDER_ID, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::PRODUCT_ID, Pap_Db_Table_Transactions::PRODUCT_ID, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::TOTAL_COST, Pap_Db_Table_Transactions::TOTAL_COST, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::TRACK_METHOD, Pap_Db_Table_Transactions::TRACK_METHOD, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_TIME, Pap_Db_Table_Transactions::FIRST_CLICK_TIME, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_IP, Pap_Db_Table_Transactions::FIRST_CLICK_IP, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_TIME, Pap_Db_Table_Transactions::LAST_CLICK_TIME, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_REFERER, Pap_Db_Table_Transactions::LAST_CLICK_REFERER, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_IP, Pap_Db_Table_Transactions::LAST_CLICK_IP, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_DATA1, Pap_Db_Table_Transactions::LAST_CLICK_DATA1, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_DATA2, Pap_Db_Table_Transactions::LAST_CLICK_DATA2, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::DATA1, Pap_Db_Table_Transactions::DATA1, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::DATA2, Pap_Db_Table_Transactions::DATA2, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::DATA3, Pap_Db_Table_Transactions::DATA3, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::DATA4, Pap_Db_Table_Transactions::DATA4, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::DATA5, Pap_Db_Table_Transactions::DATA5, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::MERCHANTNOTE, Pap_Db_Table_Transactions::MERCHANTNOTE, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::SYSTEMNOTE, Pap_Db_Table_Transactions::SYSTEMNOTE, 't');
		$selectBuilder->select->add(Pap_Db_Table_Transactions::VISITOR_ID, Pap_Db_Table_Transactions::VISITOR_ID, 't');

		$selectBuilder->from->add(Pap_Db_Table_Transactions::getName(), "t");
		$selectBuilder->from->addLeftJoin(Pap_Db_Table_Users::getName(), "pu",
            "t.".Pap_Db_Table_Transactions::USER_ID." = pu.".Pap_Db_Table_Users::ID);
		$selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu",
            "pu.".Pap_Db_Table_Users::ACCOUNTUSERID." = gu.".Gpf_Db_Table_Users::ID);
		$selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au",
            "gu.".Gpf_Db_Table_Users::AUTHID." = au.".Gpf_Db_Table_AuthUsers::ID);
		$selectBuilder->from->addLeftJoin(Gpf_Db_Table_Currencies::getName(), "c",
            "t.".Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID." = c.".Gpf_Db_Table_Currencies::ID);
		$selectBuilder->from->addLeftJoin(Pap_Db_Table_Campaigns::getName(), "ca",
            "t.".Pap_Db_Table_Transactions::CAMPAIGN_ID." = ca.".Pap_Db_Table_Campaigns::ID);

		$selectBuilder->where->add(Pap_Db_Table_Transactions::TRANSACTION_ID, '=', $transId);

		$row = $selectBuilder->getOneRow();
		 
		return $row;
	}
}

?>
