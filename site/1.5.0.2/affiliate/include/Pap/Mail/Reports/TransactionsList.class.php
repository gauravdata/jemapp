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
class Pap_Mail_Reports_TransactionsList extends Pap_Merchants_Transaction_TransactionsGrid implements Gpf_View_Grid_HasRowFilter, Pap_Mail_Reports_HasHtml {

	/**
	 * @var Pap_Stats_Params
	 */
	private $statsparams;
	private $timeOffset;
	/**
	 * @var Gpf_View_GridService_RowsIterator
	 */
	private $iterator;

	public function __construct(Pap_Stats_Params $statsparams, $timeOffset) {
		parent::__construct();
		$this->statsparams = $statsparams;
		$this->timeOffset = $timeOffset;
	}

	public function getHtml($limit = 9999) {
		$gridResponse = $this->getRows($this->getParams($limit));

		if ($gridResponse->count > 0) {
			return $this->createList();
		}
		return $this->_('No data');
	}

	/**
	 * @param $row
	 * @return DataRow or null
	 */
	public function filterRow(Gpf_Data_Row $row) {
		$row->set(Pap_Db_Table_Transactions::R_STATUS, Pap_Common_Constants::getStatusAsText($row->get(Pap_Db_Table_Transactions::R_STATUS)));
		$row->set(Pap_Db_Table_Transactions::PAYOUT_STATUS, Pap_Common_Constants::getPayoutStatusAsText($row->get(Pap_Db_Table_Transactions::PAYOUT_STATUS)));
		$row->set(Pap_Db_Table_Transactions::R_TYPE, Pap_Common_Constants::getTypeAsText($row->get(Pap_Db_Table_Transactions::R_TYPE)));
		return $row;
	}

	protected function buildWhere() {
		foreach ($this->filters as $filter) {
			$filter->setTimeOffset($this->timeOffset);
		}
		parent::buildWhere();
	}
	
	protected function buildLimit() {
		$limit = Gpf_Settings::get(Pap_Settings::REPORTS_MAX_TRANSACTIONS_COUNT);
		if ($limit >= 0) {
		  $this->_selectBuilder->limit->set(0, intval($limit));
		}
	}

	/**
	 *
	 * @return Gpf_Data_RecordSet
	 */
	protected function loadResultData() {
		$this->iterator = $this->createRowsIterator();
		return $this->initResult();
	}

	private function createList() {
		$salesTemplate = new Gpf_Templates_Template('sales_list.tpl', 'merchants');
		$salesTemplate->assignByRef('sales', $this->iterator);
		$salesTemplate->assignByRef('currency', Pap_Common_Utils_CurrencyUtils::getDefaultCurrency()->getSymbol());
		return $salesTemplate->getHTML();
	}

	/**
	 * @return Gpf_Rpc_Params
	 */
	private function getParams($limit) {
		$params = new Gpf_Rpc_Params();
		$params->add('sort_col', 'dateinserted');
		$params->add('sort_asc', false);
		$params->add('offset', 0);
		$params->add('limit', $limit);
		$params->add('columns', array(array("id"),array("id"),array("id"),array("commission"),array("totalcost"),array("orderid"),array("productid"),array("dateinserted"),array("name"),array("rtype"),array("tier"),array("commissionTypeName"),array("rstatus"),array("payoutstatus"),array("userid"),array("username"),array("firstname"),array("lastname"),array("channel")));

		$filters = array();
			
		if ($this->statsparams->isDateFromDefined()) {
			$filters[] = array('dateinserted', 'D>=', Gpf_Common_DateUtils::getDateTime($this->statsparams->getDateFrom()->toTimeStamp() + $this->timeOffset));
		}
		if ($this->statsparams->isDateToDefined()) {
			$filters[] = array('dateinserted', 'D<=', Gpf_Common_DateUtils::getDateTime($this->statsparams->getDateTo()->toTimeStamp() + $this->timeOffset));
		}
		if ($this->statsparams->isTypeDefined()) {
			$filters[] = array('rtype', 'IN', $this->statsparams->getType());
		}
		if ($this->statsparams->isAffiliateIdDefined()) {
			$filters[] = array('userid', 'E', $this->statsparams->getAffiliateId());
		}
		if ($this->statsparams->isAccountIdDefined()) {
			$filters[] = array('accountid', 'E', $this->statsparams->getAccountId());
		}

		if (count($filters) > 0) {
			$params->add('filters', $filters);
		}

		return $params;
	}
}
