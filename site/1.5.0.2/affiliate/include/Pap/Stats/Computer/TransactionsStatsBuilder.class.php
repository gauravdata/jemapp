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
class Pap_Stats_Computer_TransactionsStatsBuilder extends Pap_Stats_Computer_StatsBuilderBase {
	
	const TRANSACTIONS_PREFIX = 't';

	private $firstTierTransactions = true;

	public function __construct(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias, $firstTierTransactions = true) {
	    $this->firstTierTransactions = $firstTierTransactions;
		parent::__construct($statParams, $groupColumn, $groupColumnAlias);
	}

	protected function buildSelect($groupColumn, $groupColumnAlias) {
		$this->statsSelect->select->add(self::TRANSACTIONS_PREFIX.'.groupColumn', $groupColumnAlias);

		if ($this->firstTierTransactions) {
	        $this->statsSelect->select->add('sum(IF('.self::TRANSACTIONS_PREFIX.'.tier = 1, 1, 0))', 'count');
		} else {
	        $this->statsSelect->select->add('sum(IF('.self::TRANSACTIONS_PREFIX.'.tier != 1 , 1, 0))', 'count');
		}
		$this->statsSelect->select->add('sum('.self::TRANSACTIONS_PREFIX.'.'.Pap_Db_Table_Transactions::COMMISSION.')', Pap_Db_Table_Transactions::COMMISSION);
        $this->statsSelect->select->add('sum(IF('.self::TRANSACTIONS_PREFIX.'.tier=1, '.Pap_Db_Table_Transactions::TOTAL_COST.', 0))', Pap_Db_Table_Transactions::TOTAL_COST);
        
        $this->transactionsSelect->select->add($groupColumn, 'groupColumn');
        $this->transactionsSelect->select->add(Pap_Db_Table_Transactions::TIER, 'tier');
        $this->transactionsSelect->select->add('sum('.Pap_Db_Table_Transactions::COMMISSION.')', Pap_Db_Table_Transactions::COMMISSION);
        $this->transactionsSelect->select->add('MAX('.Pap_Db_Table_Transactions::TOTAL_COST.')', Pap_Db_Table_Transactions::TOTAL_COST);
	}
	
	protected function buildFrom() {
		$this->statsSelect->from->addSubselect($this->transactionsSelect, self::TRANSACTIONS_PREFIX);		
		$this->transactionsSelect->from->add(Pap_Db_Table_Transactions::getName());
	}
	
	protected function buildWhere(Pap_Stats_Params $statParams) {
		$statParams->addTo($this->transactionsSelect);
        if ($statParams->isTypeDefined()) {
            $this->transactionsSelect->where->add(Pap_Db_Table_Transactions::R_TYPE, 'IN', explode(',', $statParams->getType()));
        }
        if ($statParams->isStatusDefined()) {
            if (is_array($statParams->getStatus())) {
                $this->transactionsSelect->where->add(Pap_Db_Table_Transactions::R_STATUS, 'IN', $statParams->getStatus());
            } else {
                $this->transactionsSelect->where->add(Pap_Db_Table_Transactions::R_STATUS, 'IN', explode(',', $statParams->getStatus()));
            }
        }   
	}
	
	protected function buildGroupBy($groupColumn) {
		$this->statsSelect->groupBy->add(self::TRANSACTIONS_PREFIX.'.groupColumn');
        $this->transactionsSelect->groupBy->add(Pap_Db_Table_Transactions::TRANSACTION_ID);
        $this->transactionsSelect->groupBy->add(Pap_Db_Table_Transactions::TIER);
        $transactionsStatsBuilderContext = new Pap_Stats_Computer_TransactionsStatsBuilderContext($this, $groupColumn);
        Gpf_Plugins_Engine::extensionPoint('Pap_Stats_Computer_TransactionsStatsBuilder.buildGroupBy', $transactionsStatsBuilderContext);
	}
}
?>
