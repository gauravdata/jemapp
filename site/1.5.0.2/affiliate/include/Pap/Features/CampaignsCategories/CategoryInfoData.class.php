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
class Pap_Features_CampaignsCategories_CategoryInfoData extends Gpf_Object {

	/**
	 *
	 * @service campaigns_categories read
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
	 * Load category detail for transaction manager
	 *
	 * @service campaigns_categories read
	 * @param $fields
	 */
	public function categoryDetails(Gpf_Rpc_Params $params) {
		$data = new Gpf_Rpc_Data($params);

		$ids = $data->getFilters()->getFilter("id");
		 
		if (sizeof($ids) == 1) {
			$id = $ids[0]->getValue();
		}

		$category = $this->getCategoryData($id);
		if($category == null) {
			return $data;
		}

		foreach ($category as $name => $value) {
			$data->setValue($name, $value);
		}

		return $data;
	}

	/**
	 * @throws Gpf_DbEngine_TooManyRowsException
	 * @throws Gpf_DbEngine_NoRowException
	 * @param string $categoryId
	 * @return Gpf_Data_Record
	 */
	private function getCategoryData($categoryId) {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();

		$selectBuilder->select->add(Pap_Db_Table_CampaignsCategories::DESCRIPTION, Pap_Db_Table_CampaignsCategories::DESCRIPTION, 'c');
		$selectBuilder->select->add(Pap_Db_Table_CampaignsCategories::CATEGORYID, Pap_Db_Table_CampaignsCategories::CATEGORYID, 'c');
		$selectBuilder->select->add(Gpf_Db_Table_HierarchicalDataNodes::NAME, Gpf_Db_Table_HierarchicalDataNodes::NAME, 'h');
		$selectBuilder->select->add(Gpf_Db_Table_HierarchicalDataNodes::STATE, 'visible', 'h');
		
		$selectBuilder->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName(), "h");
		$selectBuilder->from->addLeftJoin(Pap_Db_Table_CampaignsCategories::getName(), "c",
            "h.".Gpf_Db_Table_HierarchicalDataNodes::CODE." = c.".Pap_Db_Table_CampaignsCategories::CATEGORYID);

		$selectBuilder->where->add(Gpf_Db_Table_HierarchicalDataNodes::TYPE, '=', Pap_Features_CampaignsCategories_Main::CAMPAIGNS_CATEGORIES_HIERARCHICAL_DATE_TYPE);
        $selectBuilder->where->add(Pap_Db_Table_CampaignsCategories::CATEGORYID,'=',$categoryId);
		
        $row = $selectBuilder->getOneRow();
		 
		return $row;
	}
}

?>
