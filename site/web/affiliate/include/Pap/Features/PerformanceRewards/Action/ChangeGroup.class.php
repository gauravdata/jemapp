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
class Pap_Features_PerformanceRewards_Action_ChangeGroup extends Pap_Features_PerformanceRewards_Action {
    /**
     *
     * @var Pap_Db_Transaction
     */
    protected $transaction;
    /**
     *
     * @var Pap_Db_UserInCommissionGroup
     */
    private $userInGroup;
    /**
     *
     * @var Pap_Features_PerformanceRewards_Rule_Transaction
     */
    protected $rule;
    
    public function execute() {
        if ($this->isUserFixedInCommissionGroup()) {
            $this->logMessage('User is fixed in another commission group. Stopped putting into commission group.');
            return;
        }
        
        $fromGroupPriority = $this->getGroupPriority($this->getUserCommissionGroup()->getCommissionGroupId());
        $targetGroupPriority = $this->getGroupPriority($this->rule->getCommissionGroupId());

        if( $fromGroupPriority > $targetGroupPriority && ($this->getUserCommissionGroup()->getStatus() == Pap_Features_PerformanceRewards_Condition::STATUS_ASCENDING)) {
            return;
        }
        
        if( $fromGroupPriority < $targetGroupPriority && ($this->getUserCommissionGroup()->getStatus() == Pap_Features_PerformanceRewards_Condition::STATUS_DESCENDING)) {
            return;
        }
        
        if(!$this->isGroupChanged()) {
            $this->logMessage('No commission group change required. User is already in target group.');
            return;
        }
        $this->recomputeCommissions();
        $this->changeGroup();
    }
    
    protected function getGroupPriority($commissionGroupId) {
        $commissionGroup = new Pap_Db_CommissionGroup();
        $commissionGroup->setId($commissionGroupId);
        $commissionGroup->load();
        return $commissionGroup->getPriority();
    }
    
    protected function logMessage($message) {
        Gpf_Log::debug($message);
    }
    
    /**
     *
     * @return Pap_Db_UserInCommissionGroup
     */
    protected function getUserCommissionGroup() {
        if($this->userInGroup !== null) {
            return $this->userInGroup;
        }
        try {
            $userInGroup = Pap_Db_Table_UserInCommissionGroup::getInstance()->getUserCommissionGroup(
                $this->getCurrentUserId(), $this->transaction->getCampaignId());
        } catch (Gpf_DbEngine_TooManyRowsException $e) {
            Gpf_Log::error(sprintf('Database not in consistent state. User %s has many commission groups', $userId));
            Pap_Db_Table_UserInCommissionGroup::removeUserFromCampaignGroups($userId, $campaignId);
            $userInGroup = new Pap_Db_UserInCommissionGroup();
            $userInGroup->setStatus(Pap_Features_PerformanceRewards_Condition::STATUS_APPROVED);
            $userInGroup->setUserId($userId);
        }
        return $this->userInGroup = $userInGroup;
    }
    
    private function isUserFixedInCommissionGroup() {
        return $this->getUserCommissionGroup()->isUserFixed();
    }
    
    private function isGroupChanged() {
        return $this->getUserCommissionGroup()->getCommissionGroupId() != $this->rule->getCommissionGroupId();
    }
    
    protected function changeGroup() {
        $newGroupId = $this->rule->getCommissionGroupId();
        $this->getUserCommissionGroup()->setCommissionGroupId($newGroupId);
        if(!$this->getUserCommissionGroup()->isPersistent()) {
            $this->getUserCommissionGroup()->insert();
            $this->logMessage(sprintf('User was added to commission group %', $newGroupId));
            return;
        }
        $this->getUserCommissionGroup()->update(array(Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID));
        $this->logMessage(sprintf('Commission group for user %s in campaign %s was changed to %s',
            $this->getCurrentUserId(), $this->transaction->getCampaignId(), $newGroupId));
    }
    
    protected function initTransactionSql(Gpf_SqlBuilder_SelectBuilder $transactionsSql) {
        $transactionsSql->from->add(Pap_Db_Table_Transactions::getName(), 't');
        $transactionsSql->from->addInnerJoin(Pap_Db_Table_Commissions::getName(), 'c', 'c.'.Pap_Db_Table_Commissions::TYPE_ID.'=t.'.
            Pap_Db_Table_Transactions::COMMISSIONTYPEID.' AND c.'.Pap_Db_Table_Commissions::TIER.'=t.'.Pap_Db_Table_Transactions::TIER);
        $transactionsSql->where->add('c.'.Pap_Db_Table_Commissions::GROUP_ID, '=',$this->rule->getCommissionGroupId());
        $transactionsSql->where->add('t.'.Pap_Db_Table_Transactions::R_STATUS, '!=', Pap_Common_Constants::STATUS_DECLINED);
        $transactionsSql->where->add('t.'.Pap_Db_Table_Transactions::COMMISSIONTYPEID, '!=', null);
        $transactionsSql->where->add('t.'.Pap_Db_Table_Transactions::PAYOUT_STATUS, '=', Pap_Common_Constants::PSTATUS_UNPAID);
        $transactionsSql->where->add('t.'.Pap_Db_Table_Transactions::R_TYPE, 'NOT IN', array(Pap_Db_Transaction::TYPE_REFUND, Pap_Db_Transaction::TYPE_CHARGE_BACK));
    }
    
    protected static function getCommissionTypeFromTransaction($transactionType) {
        if ($transactionType == Pap_Common_Constants::TYPE_RECURRING) {
            return Pap_Db_Table_Commissions::SUBTYPE_RECURRING;
        } else {
            return Pap_Db_Table_Commissions::SUBTYPE_NORMAL;
        }
    }
    
    /**
     *
     * @return Pap_Db_Commission
     */
    protected function getCommissionForTransaction() {
        $transactions = new Gpf_SqlBuilder_SelectBuilder();
        $transactions->select->add('c.'.Pap_Db_Table_Commissions::TYPE);
        $transactions->select->add('c.'.Pap_Db_Table_Commissions::VALUE);
        $transactions->where->add('t.'.Pap_Db_Table_Transactions::TRANSACTION_ID, '=', $this->transaction->getId());
        $transactions->where->add('c.'.Pap_Db_Table_Commissions::SUBTYPE,'=',self::getCommissionTypeFromTransaction($this->transaction->getType()));
        $this->initTransactionSql($transactions);
        
        $commission = new Pap_Db_Commission();
        $commission->fillFromRecord($transactions->getOneRow());
        return $commission;
    }
    
    /**
     *
     * @param $transaction
     * @return Pap_Db_Transaction
     */
    private function copyTransaction(Pap_Common_Transaction $transaction) {
        $dbTransaction = new Pap_Db_Transaction();
        foreach ($transaction->getAttributes() as $name => $value) {
            $dbTransaction->set($name, $value);
        }
        return $dbTransaction;
    }
    
    protected function recomputeCommissions() {
        try {
            $commission = $this->getCommissionForTransaction();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return;
        }
        $transaction = $this->copyTransaction( $this->transaction);
        $transaction->recompute($commission);
        $transaction->update();
        $this->logMessage(sprintf("Transaction was updated based on new commission group %s", $this->rule->getCommissionGroupId()));
        $refundTransaction = $transaction->getRefundOrChargebackTransaction();
        if (!is_null($refundTransaction) &&
                $refundTransaction->getStatus() != Pap_Common_Constants::STATUS_DECLINED &&
                $refundTransaction->getPayoutStatus() == Pap_Common_Constants::PSTATUS_UNPAID) {
            $refundTransaction->recompute($commission);
            $refundTransaction->update();
            $this->logMessage(sprintf("Refund or chargeback transaction was updated based on new commission group %s", $this->rule->getCommissionGroupId()));
        }

    }
    
    public static function toString() {
        return Gpf_Lang::_("put affiliate into commission group");
    }
        
    public function getString() {
        return sprintf(self::toString() . " %s", $this->rule->getCommissionGroupId());
    }
}
?>
