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
class Pap_Features_ActionCommission_ActionStatsBuilder extends Pap_Stats_Computer_TransactionsStatsBuilder {
	
	private $commissionTypeId;		
	
	public function __construct(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias, $commissionTypeId) {
		$this->commissionTypeId = $commissionTypeId;
		$this->init($statParams, $groupColumn, $groupColumnAlias);
	}
	
	protected function buildSelect($groupColumn, $groupColumnAlias) {
		parent::buildSelect($groupColumn, $groupColumnAlias);
		$this->statsSelect->select->add(self::TRANSACTIONS_PREFIX.'.'.Pap_Db_Table_Transactions::COMMISSIONTYPEID, Pap_Db_Table_Transactions::COMMISSIONTYPEID);
		$this->transactionsSelect->select->add(Pap_Db_Table_Transactions::COMMISSIONTYPEID);
	}
	
	protected function buildWhere(Pap_Stats_Params $statParams) {
		parent::buildWhere($statParams);
		$this->transactionsSelect->where->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_ACTION);
		$this->transactionsSelect->where->add(Pap_Db_Table_Transactions::COMMISSIONTYPEID, '=', $this->commissionTypeId);
	}
	
	protected function buildGroupBy($groupColumn) {
		parent::buildGroupBy($groupColumn);
        $this->transactionsSelect->groupBy->add(Pap_Db_Table_Transactions::COMMISSIONTYPEID);
	}
}
?>
