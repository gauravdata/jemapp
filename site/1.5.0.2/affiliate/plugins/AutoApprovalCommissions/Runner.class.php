<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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
class AutoApprovalCommissions_Runner extends Gpf_Tasks_LongTask {

    public function getName() {
        return $this->_('Auto approval commissions');
    }
    
    protected function getTransactionsList() {
        $types = array(Pap_Common_Constants::TYPE_SALE, Pap_Common_Constants::TYPE_ACTION, Pap_Common_Constants::TYPE_LEAD);
        
        $select = new Gpf_SqlBuilder_SelectBuilder();
         
        $select->select->addAll(Pap_Db_Table_Transactions::getInstance(), 't');
        $select->from->add(Pap_Db_Table_Transactions::getName(), 't');
        $select->from->addInnerJoin(Pap_Db_Table_CommissionTypeAttributes::getName(), 'cta', 
            'cta.'.Pap_Db_Table_CommissionTypeAttributes::COMMISSION_TYPE_ID.'=t.'.Pap_Db_Table_Transactions::COMMISSIONTYPEID. ' 
            and (cta.'.Pap_Db_Table_CommissionTypeAttributes::NAME."='".AutoApprovalCommissions_Main::AUTO_APPROVAL_COMMISSIONS_DAYS."' 
            and ".Pap_Db_Table_CommissionTypeAttributes::VALUE . '<>0)
            and (unix_timestamp(t.dateinserted) + (cta.'.Pap_Db_Table_CommissionTypeAttributes::VALUE.' * 86400)) <= unix_timestamp()
            ');

        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "IN", $types);
        $select->where->add(Pap_Db_Table_Transactions::R_STATUS, "=", Pap_Common_Constants::STATUS_PENDING);
        $transaction = new Pap_Common_Transaction();
        return $transaction->loadCollectionFromRecordset($select->getAllRows());
    }
    
    protected function execute() {
        $transactions = $this->getTransactionsList();
        $commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
        foreach ($transactions as $transaction) {
            $transaction->setStatus(Pap_Common_Constants::STATUS_APPROVED);
            $note = $commTypeAttr->getCommissionTypeAttribute($transaction->getCommissionTypeId(), AutoApprovalCommissions_Main::AUTO_APPROVAL_COMMISSIONS_NOTE)->getValue();
            if ($note != '') {
                $transaction->setMerchantNote($note);
            }
            $transaction->save();
            Gpf_Log::debug('Transacton id: ' . $transaction->getId() . ' is approved.' );
            $this->approveRefund($transaction->getId());
            $this->approveRecurringCommission($transaction->getId());
        }
    }

    private function approveRefund($transactionId) {
        $transaction = new Pap_Common_Transaction();
        $transaction->setId($transactionId);
        $refundTransaction = $transaction->getRefundOrChargebackTransaction();
        if (is_null($refundTransaction)) {
            Gpf_Log::debug('Transacton id: ' . $transactionId . ' does not have refund transaction.' );
            return;
        }
        if ($refundTransaction->getStatus() == Pap_Common_Constants::STATUS_PENDING) {
            $refundTransaction->setStatus(Pap_Common_Constants::STATUS_APPROVED);
            $refundTransaction->update(array(Pap_Db_Table_Transactions::R_STATUS));
            Gpf_Log::debug('Refund commission (' . $refundTransaction->getId() . ') for transacton id: ' . $transactionId . ' is approved.' );
        } 
    }

    private function approveRecurringCommission($transactionId) {
        $recurringCommission = new Pap_Db_RecurringCommission();
        $recurringCommission->setTransactionId($transactionId);
        try {
            $recurringCommission->loadFromData(array(Pap_Db_Table_RecurringCommissions::TRANSACTION_ID));
            if ($recurringCommission->getStatus() == Pap_Common_Constants::STATUS_PENDING) {
                $recurringCommission->setStatus(Pap_Common_Constants::STATUS_APPROVED);
                $recurringCommission->update(array(Pap_Db_Table_RecurringCommissions::STATUS));
                Gpf_Log::debug('Recurring commission for transacton id: ' . $transactionId . ' is approved.' );
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::debug('Transacton id: ' . $transactionId . ' does not have recurring commission.' );
        }
    }

}
?>
