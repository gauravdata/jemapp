<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class Pap_Affiliates_Reports_SubAffSaleStatsBuilder extends Pap_Stats_Computer_StatsBuilderBase {
	
	const SUBUSER_TRANS_PREFIX = 'sut';
	const USER_TRANS_PREFIX = 'ut';
	
	protected function buildSelect($groupColumn, $groupColumnAlias) {
		$this->statsSelect->select->add(self::SUBUSER_TRANS_PREFIX.'.userid', $groupColumnAlias);
		$this->statsSelect->select->add('count('.self::USER_TRANS_PREFIX.'.transid)', 'count');
		$this->statsSelect->select->add('sum('.self::USER_TRANS_PREFIX.'.commission)', 'commission');
		
		$this->transactionsSelect->select->add(Pap_Db_Table_Transactions::USERID);
		$this->transactionsSelect->select->add(Pap_Db_Table_Transactions::TRANSACTION_ID);
		$this->transactionsSelect->select->add(Pap_Db_Table_Transactions::COMMISSION);
		$this->transactionsSelect->select->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID);
	}
	
	protected function buildFrom() {
		$this->transactionsSelect->from->add(Pap_Db_Table_Transactions::getName());
		$this->statsSelect->from->addSubselect($this->transactionsSelect, self::USER_TRANS_PREFIX);
		$this->statsSelect->from->addInnerJoin(Pap_Db_Table_Transactions::getName(), 
		self::SUBUSER_TRANS_PREFIX, self::SUBUSER_TRANS_PREFIX.'.transid='.self::USER_TRANS_PREFIX.'.parenttransid');
	}
	
	protected function buildWhere(Pap_Stats_Params $statParams) {
		$statParams->addTo($this->transactionsSelect);
		$this->transactionsSelect->where->add(Pap_Db_Table_Users::ID, '=', $this->getLoggedAffId());		
		$this->transactionsSelect->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, '!=', null);
		$this->transactionsSelect->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, '<>', '');	
	}
	
	protected function buildGroupBy($groupColumn) {
		$this->statsSelect->groupBy->add(self::SUBUSER_TRANS_PREFIX.'.userid');
	}
	
	protected function getLoggedAffId() {
		return Gpf_Session::getAuthUser()->getPapUserId();
	}
}
?>
