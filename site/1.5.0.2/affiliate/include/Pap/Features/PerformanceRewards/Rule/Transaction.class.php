<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Features_PerformanceRewards_Rule_Transaction extends Pap_Features_PerformanceRewards_Rule {
    const ACTION_PUT_AFFILIATE_INTO_COMMISSION_GROUP = 'CG';
    const ACTION_PUT_AFFILIATE_INTO_COMMISSION_GROUP_RETROACTIVELY = 'CGR';
    const ACTION_PUT_ADD_BONUS = 'BC';
    
    
    /**
     *
     * @var Pap_Db_Transaction
     */
    private $transaction;

    public function __construct(Pap_Db_Transaction $transaction = null) {
        parent::__construct();
        $this->transaction = $transaction;
    }
    
    /**
     *
     * @return Pap_Db_Transaction
     */
    public function getTransaction() {
        return $this->transaction;
    }
    
    public function getUserId() {
        return $this->transaction->getUserId();
    }
    
    public function setTransactionsWhere(Gpf_SqlBuilder_WhereClause $where, $transactionTableAlias = 't') {
        $this->setSqlDateRange($where, $transactionTableAlias);
        $where->add($transactionTableAlias . '.'. Pap_Db_Table_Rules::CAMPAIGN_ID, '=', $this->getCampaignId());
    }
    
    private function setSqlDateRange(Gpf_SqlBuilder_WhereClause $where, $transactionTableAlias = 't') {
        if ($this->getDate() == Pap_Features_PerformanceRewards_Rule::DATE_ALL_UNPAID_COMMISSIONS) {
            $where->add($transactionTableAlias.'.'.Pap_Db_Table_Transactions::PAYOUT_STATUS, '=', Pap_Common_Constants::PSTATUS_UNPAID);
        }

        //TODO: implement - use dateRange object
        //$where->addDateRange($transactionTableAlias.'.'.Pap_Db_Table_Transactions::DATE_INSERTED, $dateRange);
        
        $dateRange = $this->getDateRange(new Gpf_DateTime());
        if($dateRange->isAllTime()) {
            return;
        }
        $where->add($transactionTableAlias.'.'.Pap_Db_Table_Transactions::DATE_INSERTED, '>=', $dateRange->getFrom()->toDateTime());
        $where->add($transactionTableAlias.'.'.Pap_Db_Table_Transactions::DATE_INSERTED, '<=', $dateRange->getTo()->toDateTime());
    }
}

?>
