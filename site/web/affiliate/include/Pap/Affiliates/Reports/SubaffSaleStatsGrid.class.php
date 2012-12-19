<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Pap_Affiliates_Reports_SubaffSaleStatsGrid extends Pap_Common_StatsGrid implements Gpf_View_Grid_HasRowFilter {

	private $subAffSaleCount;
	private $subAffCommissions;
	/**
	 * @var Pap_Affiliates_Reports_SubaffSaleStatsGridStatsParams
	 */
	private $subAffStatsParams;

	public function __construct() {
		parent::__construct(Pap_Stats_Table::USERID, 'u');
	}

	/**
	 * @service sub_aff_sale read
	 * @return Gpf_Rpc_Serializable
	 */
	public function getRows(Gpf_Rpc_Params $params) {
		return parent::getRows($params);
	}

	/**
	 * @service sub_aff_sale export
	 * @return Gpf_Rpc_Serializable
	 */
	public function getCSVFile(Gpf_Rpc_Params $params) {
		return parent::getCSVFile($params);
	}

	protected function createResultSelect() {
		$this->initSubAffStatsParams();
		parent::createResultSelect();
		$this->initAllSubAffStats();
	}
	
	protected function initSubAffStatsParams() {
		$this->subAffStatsParams = new Pap_Affiliates_Reports_SubaffSaleStatsGridStatsParams();
		$this->subAffStatsParams->initFrom($this->filters);
		if (!$this->subAffStatsParams->isStatusDefined()) {
			$this->subAffStatsParams->setStatus(Pap_Common_Constants::STATUS_APPROVED);
		}
	}

	/**
	 * @return Pap_Stats_Params
	 */
	protected function getStatsParameters() {
		return $this->addParamsWithDateRangeFilter($this->subAffStatsParams);
	}
	
	protected function initViewColumns() {
		$this->addViewColumn(Pap_Db_Table_Users::ID, $this->_("Affiliate name"), true);
		$this->addViewColumn(Pap_Db_Table_Users::REFID, $this->_("Referral id"), true);
		$this->addViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, $this->_("Username"), true);
		$this->addViewColumn("dateinserted", $this->_("Date of registration"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
		$this->addViewColumn("psales", $this->_("% of total sales"));
		$this->addViewColumn("pcommissions", $this->_("% of total commissions"));
		$this->addViewColumn("clicksRaw", $this->_("Raw clicks"), true);
		$this->addViewColumn("clicksUnique", $this->_("Unique clicks"), true);
		$this->addViewColumn("salesCount", $this->_("Sales"), true);
		$this->addViewColumn("commissions", $this->_("Commissions"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
		$this->addAllActionsViewColumns();
	}
	
	protected function initRequiredColumns() {
		$this->addRequiredColumn(Pap_Db_Table_Users::ID);
		$this->addRequiredColumn(Gpf_Db_Table_AuthUsers::USERNAME);
		$this->addRequiredColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME);
		$this->addRequiredColumn(Gpf_Db_Table_AuthUsers::LASTNAME);
	}

	protected function initDataColumns() {
		$this->setKeyDataColumn("u.userid");
		$this->addDataColumn('userid', "u.userid");
		$this->addDataColumn(Pap_Db_Table_Users::REFID, "u." . Pap_Db_Table_Users::REFID);
		$this->addDataColumn(Gpf_Db_Table_AuthUsers::USERNAME, "au." . Gpf_Db_Table_AuthUsers::USERNAME);
		$this->addDataColumn('dateinserted', "u.dateinserted");
		$this->addDataColumn('username', "au.username");
		$this->addDataColumn('firstname', "au.firstname");
		$this->addDataColumn('lastname', "au.lastname");
		$this->initStatColumns();
	}

	protected function initDefaultView() {
		$this->addDefaultViewColumn("userid", '', 'A');
		$this->addDefaultViewColumn("dateinserted");
		$this->addDefaultViewColumn("psales");
		$this->addDefaultViewColumn("pcommissions");
	}

	protected function initResult() {
		$result = parent::initResult();
		$result->addColumn('psales');
		$result->addColumn('pcommissions');
		return $result;
	}

	/**
	 * @param $row
	 * @return DataRow or null
	 */
	public function filterRow(Gpf_Data_Row $row) {		
		if ($this->isColumnRequired('psales') || $this->isColumnRequired('pcommissions')) {			
			$row->add('psales', $this->computePercentageValue($row->get('salesCount'), $this->subAffSaleCount));
			$row->add('pcommissions', $this->computePercentageValue($row->get('commissions'), $this->subAffCommissions));
		}
			
		return $row;
	}

	function buildFrom() {
		$this->_selectBuilder->from->add(Pap_Db_Table_Users::getName(), "u");
		$this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "u.accountuserid = gu.accountuserid");
		$this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
		$this->buildStatsFrom();
	}

	protected function buildWhere() {
		$this->_selectBuilder->where->add("u.parentuserid", "=", Gpf_Session::getAuthUser()->getPapUserId());
		$this->_selectBuilder->where->add("u.deleted", "=", Gpf::NO);
		$this->_selectBuilder->where->add("gu.rstatus", "<>", Pap_Common_Constants::STATUS_DECLINED);
	}

	/**
	 * @param Pap_Stats_Params $statParams
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	protected function getTransactionStatsSelect(Pap_Stats_Params $statParams) {
		$subAffSaleStats = new Pap_Affiliates_Reports_SubAffSaleStatsBuilder($statParams, $this->getGroupColumn(), self::GROUP_COLUMN_ALIAS);
		$subAffSaleStats->getTransactionsWhereClause()->add(Pap_Db_Table_Transactions::R_TYPE, 'in', array(Pap_Common_Constants::TYPE_SALE, Pap_Common_Constants::TYPE_CLICK));
		return $subAffSaleStats->getStatsSelect();
	}

	protected function isColumnRequired($columnName) {
		if (in_array($columnName, array('salesCount', 'commissions'))
		&& (parent::isColumnRequired('psales') || parent::isColumnRequired('pcommissions'))) {			
			return true;
		}
		return parent::isColumnRequired($columnName);
	}

	protected function initAllSubAffStats() {
		$statParams = new Pap_Stats_Params();
		$statParams->initFrom($this->filters);
		$statParams->setType(Pap_Common_Constants::TYPE_SALE . ',' . Pap_Common_Constants::TYPE_CLICK);
		
		$transactionStats = new Pap_Stats_Computer_TransactionsStatsBuilder($statParams, Pap_Db_Table_Transactions::USER_ID, Pap_Db_Table_Transactions::USERID, false);				      
		$transactionStats->getTransactionsWhereClause()->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, '!=', null);
		$transactionStats->getTransactionsWhereClause()->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, '<>', '');				
		$allSubStats = $transactionStats->getStatsSelect();
		
		try {
			$allSubStatsRow = $allSubStats->getOneRow();
			$this->subAffCommissions = $allSubStatsRow->get(Pap_Db_Table_Transactions::COMMISSION);
			$this->subAffSaleCount = $allSubStatsRow->get('count');
		} catch (Gpf_DbEngine_NoRowException $e) {			
			$this->subAffCommissions = 0;
			$this->subAffSaleCount = 0;
		}
	}

	private function computePercentageValue($value, $totalValue) {
		if ($totalValue > 0) {
			return round($value / ($totalValue / 100), 2);
		}
		return 0;
	}
}

class Pap_Affiliates_Reports_SubaffSaleStatsGridStatsParams extends Pap_Stats_Params {

	public function isAffiliateIdDefined() {
		return false;
	}
}
?>
