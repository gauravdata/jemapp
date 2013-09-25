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
class Pap_Mail_PayDayReminder_PayAffiliatesList extends Pap_Merchants_Payout_PayAffiliatesGrid {

	private $approvedCommissions = 0;
	private $pendingCommissions = 0;
	private $declinedCommissions = 0;
	private $amountToPay = 0;
	private $html;
	/**
	 * @var Gpf_View_GridService_RowsIterator
	 */
	private $iterator;

	public function __construct() {
		parent::__construct();
		$this->initialize();
	}

	public function getAmountToPay() {
		return $this->round($this->amountToPay);
	}
	public function getApprovedCommissions() {
		return $this->round($this->approvedCommissions);
	}

	public function getPendingCommissions() {
		return $this->round($this->pendingCommissions);
	}

	public function getDeclinedCommissions() {
		return $this->round($this->declinedCommissions);
	}

	public function getHtml() {
		return $this->html;
	}

	/**
	 * @param $row
	 * @return DataRow or null
	 */
	public function filterRow(Gpf_Data_Row $row) {
		$row = parent::filterRow($row);
		$this->processValues($row);
		return $row;
	}
	
	/**
	 * @return Gpf_Data_RecordSet
	 */
	protected function loadResultData() {
		$this->iterator = $this->createRowsIterator();
		return $this->initResult();
	}

	private function createList() {
		$payAffTemplate = new Gpf_Templates_Template('pay_affiliates_list.tpl', 'merchants');
		$payAffTemplate->assignByRef('payaffiliates', $this->iterator);
		$payAffTemplate->assignByRef('currency', Pap_Common_Utils_CurrencyUtils::getDefaultCurrency()->getSymbol());
		return $payAffTemplate->getHTML();
	}

	/**
	 * @return Gpf_Rpc_Params
	 */
	private function getParams() {
		$params = new Gpf_Rpc_Params();
		$params->add('sort_col', 'name');
		$params->add('sort_asc', true);
		$params->add('offset', 0);
		$params->add('limit', 9999);
		$params->add('columns', array(array("id"),array("id"),array("userid"),array("username"),array("firstname"),array("lastname"),array("username"),array("commission"),array("pendingAmount"),array("declinedAmount"),array("minimumpayout"),array("payoutMethod"),array("payoutData")));

		return $params;
	}
	
	private function initialize() {
		$gridResponse = $this->getRows($this->getParams());
		if ($gridResponse->count > 0) {
			$this->html = $this->createList();
			return;
		}
		$this->html = $this->_('No affiliates to pay');
	}

	private function processValues(Gpf_Data_Row $row) {
		$this->approvedCommissions += $row->get(Pap_Db_Table_Transactions::COMMISSION);
		$this->pendingCommissions += $row->get('pendingAmount');
		$this->declinedCommissions += $row->get('declinedAmount');
		if (!$row->contains('amounttopay')) {
			$row->add('amounttopay', $row->get(Pap_Db_Table_Transactions::COMMISSION));
		}
		$this->amountToPay += $row->get('amounttopay');
			
		$row->set(Pap_Db_Table_Transactions::COMMISSION, $this->round($row->get(Pap_Db_Table_Transactions::COMMISSION)));
		$row->set('pendingAmount', $this->round($row->get('pendingAmount')));
		$row->set('declinedAmount', $this->round($row->get('declinedAmount')));
		$row->set('amounttopay', $this->round($row->get('amounttopay')));
	}

	private function round($value) {
		return round($value, Pap_Common_Utils_CurrencyUtils::getDefaultCurrencyPrecision());
	}
}
