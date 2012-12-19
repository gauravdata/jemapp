<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic, Juraj Simon
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
class Pap_Features_PerformanceRewards_Action_ChangeGroupRetroactively extends Pap_Features_PerformanceRewards_Action_ChangeGroup {

    /**
     *
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function getTransactionsSelect() {
        $transactions = new Gpf_SqlBuilder_SelectBuilder();
        $transactions->select->addAll(Pap_Db_Table_Transactions::getInstance(), 't');
        $transactions->select->add('c.'.Pap_Db_Table_Commissions::TYPE);
        $transactions->select->add('c.'.Pap_Db_Table_Commissions::VALUE);
        $transactions->where->add('t.'.Pap_Db_Table_Transactions::USER_ID, '=', $this->getCurrentUserId());        
        $this->initTransactionSql($transactions);
        return $transactions;
    }
    
    protected function recomputeCommissions() {
        $transactions = $this->getTransactionsSelect();
        $this->rule->setTransactionsWhere($transactions->where, 't');

        foreach ($transactions->getAllRowsIterator() as $record) {       	
            $newCommission = new Pap_Db_Commission();
            $newCommission->setTypeId($record->get(Pap_Db_Table_Transactions::COMMISSIONTYPEID));
            $newCommission->setTier($record->get(Pap_Db_Table_Transactions::TIER));
            $newCommission->setGroupId($this->rule->getCommissionGroupId());          
            if ($record->get(Pap_Db_Table_Transactions::R_TYPE) == Pap_Common_Constants::TYPE_RECURRING) { 
            	$newCommission->setSubtype(Pap_Db_Table_Commissions::SUBTYPE_RECURRING);
            } else {
            	$newCommission->setSubtype(Pap_Db_Table_Commissions::SUBTYPE_NORMAL);
            }
            try {
            	$newCommission->loadFromData();
            } catch (Exception $e) {
            	$this->logMessage(sprintf("Error loading commission (%s)", $e->getMessage()));
            	return;
            }            
            $transaction = new Pap_Db_Transaction();
            $transaction->fillFromRecord($record);
            $transaction->recompute($newCommission);            
            $transaction->update();         
            $refundTransaction = $transaction->getRefundOrChargebackTransaction();
            if (!is_null($refundTransaction) &&
                    $refundTransaction->getStatus() != Pap_Common_Constants::STATUS_DECLINED &&
                    $refundTransaction->getPayoutStatus() == Pap_Common_Constants::PSTATUS_UNPAID) {
                $refundTransaction->recompute($newCommission);
                $refundTransaction->update();
            }
        }
        $this->logMessage(sprintf("Transactions were updated based on new commission group %s", $this->rule->getCommissionGroupId()));
    }
    
    public static function toString() {
        return Gpf_Lang::_("put affiliate into commission group (retroactively)");
    }
        
    public function getString() {
        return sprintf(self::toString() . " %s", $this->rule->getCommissionGroupId());
    }
}
?>
