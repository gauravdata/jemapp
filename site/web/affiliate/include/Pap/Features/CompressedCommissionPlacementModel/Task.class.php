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
class Pap_Features_CompressedCommissionPlacementModel_Task extends Gpf_Tasks_LongTask {

    /**
     * @var Gpf_DbEngine_Row_Collection
     */
    private $affectedTransactions;

    /**
     * @var array
     */
    private $userIds;

    public function getName() {
        return $this->_('Compressed commission');
    }

    protected function execute() {
        $json = new Gpf_Rpc_Json();
        $processor = new Pap_Features_CompressedCommissionPlacementModel_Processor();
        if ($this->isPending('initAffiliatesList', $this->_('Initialization affiliates list'))) {
            $this->debug('initAffiliatesList');
            $this->userIds = $processor->initAffiliates();
            $this->setParams($json->encode($this->userIds));
            $this->setDone();
        }

        if ($this->isPending('initTransactionsList', $this->_('Initialization transactions list'))) {
            $this->debug('initTransactionsList');
            $this->userIds = $json->decode($this->getParams());
            $this->affectedTransactions = $processor->initTransactions($this->userIds);
            $this->setParams($json->encode($this->getTransactionIdsFromCollection($this->affectedTransactions)));
            $this->setDone();
        }
        $affectedTransactionIds = $this->getCollectionFromIds($json->decode($this->getParams()));
        $this->debug('process transactions');

        while ($affectedTransactionIds->getSize() > 0) {
            if ($this->isPending($this->getFirstElement($affectedTransactionIds)->getId(), $this->_('Compressed transaction: %s', $this->getFirstElement($affectedTransactionIds)->getId()))) {

                $processor->processFirstTransaction($affectedTransactionIds);
                $this->setDone();
            }
            else {
                $processor->removeByTransactionId($this->getFirstElement($affectedTransactionIds)->getId(), $affectedTransactionIds);
            }
        }
        $this->debug('finish task');
        $this->forceFinishTask();
    }

    /**
     * @param Gpf_DbEngine_Row_Collection $affectedTransactions
     * @return array
     */
    private function getTransactionIdsFromCollection(Gpf_DbEngine_Row_Collection $affectedTransactions) {
        $transactionIds = array();
        foreach ($affectedTransactions as $transaction) {
            $transactionIds[] = $transaction->getId();
        }
        return $transactionIds;
    }

    /**
     * @param array $transactionIds
     * @return Gpf_DbEngine_Row_Collection
     */
    private function getCollectionFromIds($transactionIds) {
        $collection = new Gpf_DbEngine_Row_Collection();
        foreach ($transactionIds as $transactionId) {
            $row = new Pap_Db_Transaction();
            $row->setId($transactionId);
            $row->load();
            $collection->add($row);
        }
        return $collection;
    }

    private function getFirstElement($array) {
        foreach ($array as $element) {
            return $element;
        }
        return null;
    }

    protected function debug($message) {
        parent::debug('Compress commission task: ' . $message);
    }
}
?>
