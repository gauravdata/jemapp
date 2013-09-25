<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
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
 * @package PostAffiliatePro plugins
 */
class Pap_Features_CompressedCommissionPlacementModel_Processor extends Gpf_Object {

    private $affectedTransactionsCount;
    private $changedTransactionsCount;
    private $addedTransactionsCount;

    /*
     * @return string
     */
    public static function getRecurrencePreset() {
        switch (Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE)) {
            case Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE_WEEKLY:
                return 'LW';
            case Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE_MONTHLY:
                return 'LM';
        }
    }

    /**
     *
     * @return Gpf_DbEngine_Row_Collection
     */
    public function getAffectedAffiliatesList($filters = null) {
        if (is_null($filters)) {
            $filters = array();
            $filter = new Gpf_Data_Filter("reachedCondition", "E", Gpf::NO);
            $filters[] = $filter->toObject();
        }

        $params = new Gpf_Rpc_Params();
        $params->add('filters', $filters);
        $params->add('columns', array(array('id'),array('id')));
        $params->add('limit', 100000);

        $grid = new Pap_Features_CompressedCommissionPlacementModel_PlacementOverviewGrid();
        $response = $grid->getRows($params);

        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->loadFromObject($response->rows);

        $user = new Pap_Db_User();

        return $user->loadCollectionFromRecordset($recordSet);
    }

    /**
     * @param array $userIds
     * @param array $orderIds
     * @return Gpf_DbEngine_Row_Collection
     */
    public function getAffectedTransactionsList($userIds, $orderIds = array()) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
        $select->from->add(Pap_Db_Table_Transactions::getName());

        $dateRangeFilter = new Gpf_SqlBuilder_Filter();
        $dateRange = $dateRangeFilter->decodeDatePreset(Pap_Features_CompressedCommissionPlacementModel_Processor::getRecurrencePreset());

        $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, '>', $dateRange['dateFrom']);
        $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, '<', $dateRange['dateTo']);
        $select->where->add(Pap_Db_Table_Transactions::USER_ID, 'IN',  $userIds);
        if (!is_null($orderIds) && count($orderIds) > 0) {
            $compoundCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
            foreach ($orderIds as $orderId) {
                $compoundCondition->add(Pap_Db_Table_Transactions::ORDER_ID, 'LIKE',  '%'.$orderId.'%', 'OR');
            }
            $select->where->addCondition($compoundCondition);
        }

        $select->orderBy->add(Pap_Db_Table_Transactions::TIER);

        $transaction = new Pap_Db_Transaction();

        $transactionsRecordSet = $select->getAllRows();

        $unpaidTransactions = new Gpf_Data_RecordSet();
        $unpaidTransactions->setHeader($transactionsRecordSet->getHeader());

        foreach ($transactionsRecordSet as $trans) {
            if ($trans->get(Pap_Db_Table_Transactions::PAYOUT_STATUS) == Pap_Common_Constants::PSTATUS_UNPAID) {
                $unpaidTransactions->add($trans);
            } else {
                $this->log('Removing paid transaction from affected transactions: ' . $trans->get(Pap_Db_Table_Transactions::TRANSACTION_ID));
            }
        }
        return $transaction->loadCollectionFromRecordset($unpaidTransactions);
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @return Pap_Db_Commission
     */
    protected function getCommissionForTransaction(Pap_Db_Transaction $transaction) {
        $commission = new Pap_Db_Commission();
        $commission->setCommissionTypeId($transaction->getCommissionTypeId());
        $commission->setGroupId($transaction->getCommissionGroupId());
        $commission->setTier($transaction->getTier());
        try {
            $commission->loadFromData(array(Pap_Db_Table_Commissions::TYPE_ID, Pap_Db_Table_Commissions::GROUP_ID));
        } catch (Gpf_Exception $e) {
            $userInGroup = Pap_Db_Table_UserInCommissionGroup::getInstance()->getUserCommissionGroup($transaction->getUserId(), $transaction->getCampaignId());
            $commission->setGroupId($userInGroup->getCommissionGroupId());
            try {
                $commission->loadFromData(array(Pap_Db_Table_Commissions::TYPE_ID, Pap_Db_Table_Commissions::GROUP_ID, Pap_Db_Table_Commissions::TIER));
            } catch (Gpf_Exception $e) {
                throw new Gpf_Exception($this->_('Unable to find commision for transaction id=' . $transaction->getId()));
            }
        }

        return $commission;
    }

    /**
     * @param Pap_Db_Transaction $transaction
     */
    private function declineTransaction(Pap_Db_Transaction $transaction) {
        $this->log('Decline transaction: ' . $transaction->getId());
        $transaction->setStatus(Pap_Common_Constants::STATUS_DECLINED);
        $transaction->setParentTransactionId('');
        $transaction->update(array(Pap_Db_Table_Transactions::R_STATUS, Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID));
    }

    /**
     * @param Pap_Db_Transaction $transaction
     */
    private function removeTransaction(Pap_Db_Transaction $transaction) {
        $this->log('Removing transaction: ' . $transaction->getId());
        $transaction->delete();
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @param boolean $simulation
     * @return string
     */
    private function processAffectedTransaction(Pap_Db_Transaction $transaction, $simulation) {
        if (!$simulation) {
            switch (Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::ACTION) ) {
                case 'r':
                    $this->removeTransaction($transaction);
                    break;
                case 'd':
                    $this->declineTransaction($transaction);
                    break;
            }
        }
        $this->affectedTransactionsCount++;
        return $this->outputAffectedTransacion($transaction);
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @return Pap_Db_Transaction
     */
    private function getParentTransaction(Pap_Db_Transaction $transaction) {
        $parentTransaction = new Pap_Db_Transaction();
        $parentTransaction->setId($transaction->getParentTransactionId());
        try {
            $parentTransaction->load();
        } catch (Gpf_Exception $e) {
            return null;
        }
        return $parentTransaction;
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @param Gpf_DbEngine_Row_Collection $affectedTransactions
     * @return Pap_Db_Transaction
     */
    protected function getChildTransaction(Pap_Db_Transaction $transaction, Gpf_DbEngine_Row_Collection $affectedTransactions) {
        foreach ($affectedTransactions as $affectedTransaction) {
            if ($affectedTransaction->getParentTransactionId() == $transaction->getId() &&
            $affectedTransaction->getType() == $transaction->getType()) {
                return $affectedTransaction;
            }
        }

        $childTransaction = new Pap_Db_Transaction();
        $childTransaction->setType($transaction->getType());
        $childTransaction->setParentTransactionId($transaction->getId());
        try {
            $childTransaction->loadFromData(array(Pap_Db_Table_Transactions::R_TYPE, Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID));
        } catch (Gpf_Exception $e) {
            return null;
        }
        return  $childTransaction;
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @param Gpf_DbEngine_Row_Collection $affectedTransactions
     * @return boolean
     */
    private function isAffectedTransaction(Pap_Db_Transaction $transaction, Gpf_DbEngine_Row_Collection $affectedTransactions) {
        foreach ($affectedTransactions as $affectedTransaction) {
            if ($affectedTransaction->getId() == $transaction->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Gpf_DbEngine_Row_Collection $affectedTransactions
     * @param $transactionId
     * @return Pap_Db_Transaction
     */
    private function getAffectedTransactionById($transactionId, Gpf_DbEngine_Row_Collection $affectedTransactions) {
        foreach ($affectedTransactions as $affectedTransaction) {
            if ($affectedTransaction->getId() == $transactionId) {
                return $affectedTransaction;
            }
        }
        throw new Pap_Features_CompressedCommissionPlacementModel_TransactionNotFoundException('Transaction not found');
    }

    /**
     * @param $transactionId
     * @param Gpf_DbEngine_Row_Collection $affectedTransactions
     */
    public function removeByTransactionId($transactionId, Gpf_DbEngine_Row_Collection $affectedTransactions) {
        foreach ($affectedTransactions as $key => $transaction) {
            if ($transaction->getId() == $transactionId) {
                $affectedTransactions->remove($key);
            }
        }
    }

    /**
     * @param Gpf_DbEngine_Row_Collection $rowCollection
     * @return Pap_Db_Transaction
     */
    public function getFirstTransaction(Gpf_DbEngine_Row_Collection $rowCollection) {
        foreach ($rowCollection as $row) {
            return $row;
        }
        return null;
    }

    /**
     * @return Array
     */
    public function initAffiliates() {
        $affectedUsers = $this->getAffectedAffiliatesList();
        $userIds = array();
        foreach ($affectedUsers as $affectedUser) {
            $userIds[] = $affectedUser->getId();
        }
        return $userIds;
    }

    /**
     * @param Array $userIds
     */
    public function initTransactions($userIds) {
        if (count($userIds) == 0) {
            return new Gpf_DbEngine_Row_Collection();
        }
        $affectedTransactions = $this->getAffectedTransactionsList($userIds);
        return $affectedTransactions;
    }

    /**
     * @param array $filtersArray
     * @return array
     */
    private function getOrderIdsFromFilters($filtersArray = null) {
        if (is_null($filtersArray)) {
            return array();
        }
        $filterCollection = new Gpf_Rpc_FilterCollection();
        foreach ($filtersArray as $filter) {
            $filterCollection->add($filter);
        }

        $filters = $filterCollection->getFilter('orderid');
        if (count($filters) == 0) {
            return array();
        }
        $filter = $filters[0];

        return array_map('trim', preg_split("/[,;(\n)]/", $filter->getValue()));
    }

    public function recalculate($filters = null, $simulation = false) {
        $this->affectedTransactionsCount = 0;
        $this->changedTransactionsCount = 0;
        $this->addedTransactionsCount = 0;

        $affectedUsers = $this->getAffectedAffiliatesList($filters);
        $userIds = array();
        foreach ($affectedUsers as $affectedUser) {
            $userIds[] = $affectedUser->getId();
        }
        $output = '';
        if (count($userIds) == 0) {
            $this->log('No affiliates for recalculate commissions.');
            $output = $this->output($this->_('No affiliates for recalculate commissions.'), 'font-weight: bold');
            return $output;
        }
        $orderIds = $this->getOrderIdsFromFilters($filters);
        $affectedTransactions = $this->getAffectedTransactionsList($userIds, $orderIds);
        if ($affectedTransactions->getSize() == 0) {
            $this->log('No unpaid transactions of affected affiliates for recalculate commissions.');
            $output = $this->output($this->_('No unpaid transactions of affected affiliates for recalculate commissions.'), 'font-weight: bold');
            return $output;
        }
        if ($simulation) {
            $output .= $this->outputln($this->_('Simulation of recalculate transacions:'), 'font-weight: bold');
            $output .= '<br />';
        } else {
            $output .= $this->outputln($this->_('Recalculate transactions:'), 'font-weight: bold');
            $output .= '<br />';
        }
        $this->log('Affected transactions: ' . $affectedTransactions->getSize());
        while ($affectedTransactions->getSize() > 0) {
            $output .= $this->processFirstTransaction($affectedTransactions, $simulation);
        }

        $output .= $this->outputSummary();

        return $output;
    }

    /**
     * @param Pap_Db_Transaction Gpf_DbEngine_Row_Collection
     * @param boolean $simulation
     * @return String
     */
    public function processFirstTransaction(Gpf_DbEngine_Row_Collection $affectedTransactions, $simulation = false) {
        $output = '';
        $transaction = $this->getFirstTransaction($affectedTransactions);
        $this->log('Processing transaction: ' . $transaction->getId());

        $output .= $this->processAffectedTransaction($transaction, $simulation);

        $this->removeByTransactionId($transaction->getId(), $affectedTransactions);

        $tier = $transaction->getTier();
        $maxOriginalTier = $tier;
        $parentTransactionId = $transaction->getParentTransactionId();
        $lastUserId = $transaction->getUserId();
        $lastTransaction = $transaction;

        $childTransaction = $this->getChildTransaction($transaction, $affectedTransactions);
        if ($childTransaction == null) {
            $this->log('Child transaction not found.');
        }
        while($childTransaction != null) {
            $this->log('Processing child transaction: ' . $childTransaction->getId());
            $childTransaction->getTier();
            if ($maxOriginalTier < $childTransaction->getTier()) {
                $maxOriginalTier = $childTransaction->getTier();
            }
            if ($this->isAffectedTransaction($childTransaction, $affectedTransactions)) {
                $this->log('Transaction is affected.');
                $output .= $this->processAffectedTransaction($childTransaction, $simulation);

                $this->removeByTransactionId($childTransaction->getId(), $affectedTransactions);
            } else {
                $this->log('Transaction recomputing...');
                if ($childTransaction->getPayoutStatus() == Pap_Common_Constants::PSTATUS_PAID) {
                    $this->log('Skipping paid transaction, transaction not recomputed: ' . $childTransaction->getId());
                    $output .= $this->outputPaidTransacion($childTransaction);
                    $tier = $childTransaction->getTier();
                    $parentTransactionId = $childTransaction->getId();
                    $childTransaction = $this->getChildTransaction($childTransaction, $affectedTransactions);
                    $tier++;
                    continue;
                }
                $originalTier = $childTransaction->getTier();
                $originalCommission = $childTransaction->getCommission();
                $childTransaction->setTier($tier);
                if ($tier != 1) {
                    $childTransaction->setParentTransactionId($parentTransactionId);
                } else {
                    $childTransaction->setParentTransactionId('');
                }
                try {
                    $commission = $this->getCommissionForTransaction($childTransaction);
                    $childTransaction->recompute($commission);
                    if (!$simulation) {
                        $this->log('Saving transaction, new commission: ' . $childTransaction->getCommission() . ', new tier: ' . $childTransaction->getTier());
                        $childTransaction->update();
                    }
                    $this->changedTransactionsCount++;
                    $output .= $this->outputChangedTransacion($childTransaction, $originalCommission, $originalTier);

                    $output .= $this->findAndRecomputeRefundTransaction($childTransaction, $commission, $tier, $simulation);
                } catch (Gpf_Exception $e) {
                    $output .= $this->_('Error during performing compression model calculations: %s', $e->getMessage());
                    Gpf_Log::error($this->_('Error during performing compression model calculations: %s', $e->getMessage()));
                }
                $tier++;
                $parentTransactionId = $childTransaction->getId();
            }
            $lastUserId = $childTransaction->getUserId();
            $lastTransaction = $childTransaction;
            $childTransaction = $this->getChildTransaction($childTransaction, $affectedTransactions);
        }
        $output .= $this->addRemainingCommissionsForParents($tier, $maxOriginalTier, $lastUserId, $lastTransaction, $simulation);
        return $output;
    }

    private function addRemainingCommissionsForParents($tier, $maxTier, $lastUserId, Pap_Db_Transaction $lastTransaction, $simulation) {
        $output = '';
        $output .= $this->outputln($this->_('Creating remaining commissions for tiers: '. $tier . ' - '. $maxTier));
        for ($tier; $tier <= $maxTier; $tier++) {
            $reachedConditionUserIds = $this->getReachedConditionAffiliateIds();
            if (is_null($userId = $this->getParentWhoReachedCondition($lastUserId, $reachedConditionUserIds))) {
                $output .= $this->outputln($this->_('No next parent affiliate who reached condition.'));
                return $output;
            }
        
            $lastTransaction->setParentTransactionId($lastTransaction->getId());
            $lastTransaction->setId('');
            
            $lastTransaction->setTier($tier);
            $lastTransaction->setUserId($userId);
            try {
                $commission = $this->getCommissionForTransaction($lastTransaction);
                $lastTransaction->recompute($commission);
                if (!$simulation) {
                    $this->log('Inserting new transaction commission: ' . $lastTransaction->getCommission() . ', new tier: ' . $lastTransaction->getTier() . 'for userId: ' . $lastTransaction->getUserId());
                    $lastTransaction->insert();
                }
                $this->addedTransactionsCount++;
                $output .= $this->outputAddedTransacion($lastTransaction);
                $lastUserId = $userId;
            } catch (Gpf_Exception $e) {
                $output .= $this->outputln($this->_('Error during performing compression model calculations: %s, probably commission settings are changed.', $e->getMessage()));
                Gpf_Log::error($this->_('Error during performing compression model calculations: %s, probably commission settings are changed.', $e->getMessage()));
                return $output; 
            } 
        }
        return $output;
    }

    /**
     * @return array
     */
    private function getReachedConditionAffiliateIds() {
        $filters = array();
        $filter = new Gpf_Data_Filter("reachedCondition", "E", Gpf::YES);
        $filters[] = $filter->toObject();
        
        $affectedUsers = $this->getAffectedAffiliatesList($filters);
        
        $userIds = array();
        foreach ($affectedUsers as $affectedUser) {
            $userIds[] = $affectedUser->getId();
        }
        return $userIds;
    }
    
    private function getParentWhoReachedCondition($userId, array $reachedConditionUserIds) {
        $user = new Pap_Common_User();
        $user->setId($userId);
        $user->load();
        if (is_null($user->getParentUserId()) || $user->getParentUserId() == '') {
            return null;
        }
        if (in_array($user->getParentUserId(), $reachedConditionUserIds)) {
            return $user->getParentUserId();
        }
        return $this->getParentWhoReachedCondition($user->getParentUserId(), $reachedConditionUserIds);
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @param Pap_Db_Commission $commission
     * @param $tier
     * @param boolean $simulation
     * @return String
     */
    private function findAndRecomputeRefundTransaction(Pap_Db_Transaction $transaction, Pap_Db_Commission $commission, $tier, $simulation) {
        $refundTransaction = $transaction->getRefundOrChargebackTransaction();
        if (!is_null($refundTransaction) &&
        $refundTransaction->getStatus() != Pap_Common_Constants::STATUS_DECLINED &&
        $refundTransaction->getPayoutStatus() == Pap_Common_Constants::PSTATUS_UNPAID) {
            $refundTransaction->setTier($tier);
            $refundTransaction->recompute($commission);
            if (!$simulation) {
                $this->log('Saving chargeback/refund transaction, new commission: ' . $refundTransaction->getCommission() . ', new tier: ' . $refundTransaction->getTier());
                $refundTransaction->update();
            }
            $this->changedTransactionsCount++;
            return $this->outputChangedTransacion($transaction, $originalCommission, $originalTier);
        }
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @return string
     */
    private function outputAffectedTransacion(Pap_Db_Transaction $transaction) {
        $output = '';
        switch (Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::ACTION) ) {
            case 'r':
                $output .= $this->outputln($this->_('Removed transaction:'), 'font-weight: bold; color: red;');
                break;
            case 'd':
                $output .= $this->outputln($this->_('Declined transaction:'), 'font-weight: bold; color: red;');
                break;
        }
        $output .= $this->output($this->_('Transaction ID'), 'font-weight: bold;') . ': ' . $transaction->getId() . ', ';
        $output .= $this->output($this->_('Order ID'), 'font-weight: bold;') . ': ' . $transaction->getOrderId() . ', ';
        $output .= $this->output($this->_('Affiliate'), 'font-weight: bold;') . ': ' . $transaction->getUserId() . ', ';
        $output .= $this->output($this->_('TotalCost'), 'font-weight: bold;') . ': ' . $transaction->getTotalCost() . ', ';
        $output .= $this->output($this->_('Commission'), 'font-weight: bold;') . ': ' . $transaction->getCommission() . ', ';
        $output .= $this->output($this->_('Tier'), 'font-weight: bold;') . ': ' . $transaction->getTier();
        $output .= '<br />';
        return $output;
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @return string
     */
    private function outputPaidTransacion(Pap_Db_Transaction $transaction) {
        $output = '';
        $output .= $this->outputln($this->_('Paid transaction (not changed):'), 'font-weight: bold; color: blue;');
        $output .= $this->output($this->_('Transaction ID'), 'font-weight: bold;') . ': ' . $transaction->getId() . ', ';
        $output .= $this->output($this->_('Order ID'), 'font-weight: bold;') . ': ' . $transaction->getOrderId() . ', ';
        $output .= $this->output($this->_('Affiliate'), 'font-weight: bold;') . ': ' . $transaction->getUserId() . ', ';
        $output .= $this->output($this->_('TotalCost'), 'font-weight: bold;') . ': ' . $transaction->getTotalCost() . ', ';
        $output .= $this->output($this->_('Commission'), 'font-weight: bold;') . ': ' . $transaction->getCommission() . ', ';
        $output .= $this->output($this->_('Tier'), 'font-weight: bold;') . ': ' . $transaction->getTier();
        $output .= '<br />';
        return $output;
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @param $originalCommission
     * @param $originalTier
     * @return string
     */
    private function outputChangedTransacion(Pap_Db_Transaction $transaction, $originalCommission, $originalTier) {
        $output = '';
        $output .= $this->outputln($this->_('Changed transaction:'), 'font-weight: bold; color: green;');
        $output .= $this->output($this->_('Transaction ID'), 'font-weight: bold;') . ': ' . $transaction->getId() . ', ';
        $output .= $this->output($this->_('Order ID'), 'font-weight: bold;') . ': ' . $transaction->getOrderId() . ', ';
        $output .= $this->output($this->_('Affiliate'), 'font-weight: bold;') . ': ' . $transaction->getUserId() . ', ';
        $output .= $this->output($this->_('TotalCost'), 'font-weight: bold;') . ': ' . $transaction->getTotalCost() . ', ';
        $output .= $this->output($this->_('New Commission'), 'font-weight: bold;') . ': ' . $transaction->getCommission() . ', ';
        $output .= $this->output($this->_('Old Commission'), 'font-weight: bold;') . ': ' . $originalCommission . ', ';
        $output .= $this->output($this->_('New Tier'), 'font-weight: bold;') . ': ' . $transaction->getTier() . ', ';
        $output .= $this->output($this->_('Old Tier'), 'font-weight: bold;') . ': ' . $originalTier;
        $output .= '<br />';
        return $output;
    }

    /**
     * @param Pap_Db_Transaction $transaction
     * @return string
     */
    private function outputAddedTransacion(Pap_Db_Transaction $transaction) {
        $output = '';
        $output .= $this->outputln($this->_('Added transaction:'), 'font-weight: bold; color: green;');
        $output .= $this->output($this->_('Transaction ID'), 'font-weight: bold;') . ': ' . $transaction->getId() . ', ';
        $output .= $this->output($this->_('Parent transaction ID'), 'font-weight: bold;') . ': ' . $transaction->getParentTransactionId() . ', ';
        $output .= $this->output($this->_('Order ID'), 'font-weight: bold;') . ': ' . $transaction->getOrderId() . ', ';
        $output .= $this->output($this->_('Affiliate'), 'font-weight: bold;') . ': ' . $transaction->getUserId() . ', ';
        $output .= $this->output($this->_('TotalCost'), 'font-weight: bold;') . ': ' . $transaction->getTotalCost() . ', ';
        $output .= $this->output($this->_('Commission'), 'font-weight: bold;') . ': ' . $transaction->getCommission() . ', ';
        $output .= $this->output($this->_('Tier'), 'font-weight: bold;') . ': ' . $transaction->getTier();
        $output .= '<br />';
        return $output;
    }


    private function outputSummary() {
        $output = '<br />';
        switch (Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::ACTION) ) {
            case 'r':
                $output .= $this->output($this->_('Removed transactions:'), 'font-weight: bold;');
                break;
            case 'd':
                $output .= $this->output($this->_('Declined transactions:'), 'font-weight: bold;');
                break;
        }
        $output .= $this->outputln(' ' . $this->affectedTransactionsCount);
        $output .= $this->output($this->_('Changed transactions:'), 'font-weight: bold;');
        $output .= $this->outputln(' ' . $this->changedTransactionsCount);
        $output .= $this->output($this->_('Added transactions:'), 'font-weight: bold;');
        $output .= $this->outputln(' ' . $this->addedTransactionsCount);
        $output .= '<br />';
        return $output;
    }

    private function outputln($text, $style = null) {
        return $this->output($text, $style).'<br />';
    }

    private function output($text, $style = null) {
        if ($style == null) {
            return $text;
        }
        return '<span style=\''. $style .'\'>' . $text . '</span>';
    }

    private function log($message) {
        Gpf_Log::debug('Compress commission processor: ' . $message);
    }
}
?>
