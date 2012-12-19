<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
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
class Pap_Tracking_Common_UpdateAllCommissions extends Pap_Tracking_Common_SaveAllCommissions implements Pap_Tracking_Common_Saver {

    /*
     * @var array<Pap_Common_Transaction>
     */
    protected $transactions = array();

    public function process(Pap_Contexts_Tracking $context) {
        parent::save($context);
    }

    public function saveChanges() {
        foreach ($this->transactions as $transaction) {
            $transaction->save();
        }
    }

    protected function saveTransaction(Pap_Common_Transaction $transaction, $dateInserted) {
        try {
            $transactionClone = $this->getCachedTransaction($transaction);

            $transaction->setId($transactionClone->getId());
            $transaction->setPersistent($transactionClone->isPersistent());
             
            $transaction->setClickCount($transaction->getClickCount()
            + $transactionClone->getClickCount());
            $transaction->setCommission($transaction->getCommission()
            + $transactionClone->getCommission());
            $transaction->setDateInserted($dateInserted);
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
        $this->transactions[$this->hashTransaction($transaction)] = $transaction;
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @return Pap_Common_Transcation
     */
    protected function getClonedTransactionFromDb(Pap_Common_Transaction $transaction) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::USER_ID, '=', $transaction->getUserId());
        $select->where->add(Pap_Db_Table_Transactions::CAMPAIGN_ID, '=', $transaction->getCampaignId());
        $select->where->add(Pap_Db_Table_Transactions::BANNER_ID, '=', $transaction->getBannerId());
        $select->where->add(Pap_Db_Table_Transactions::CHANNEL, '=', $transaction->getChannel());
        $select->where->add(Pap_Db_Table_Transactions::R_STATUS, '=', $transaction->getStatus());
        $select->where->add(Pap_Db_Table_Transactions::TIER, '=', $transaction->getTier());
        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, '=', $transaction->getType());
        $select->where->add(Pap_Db_Table_Transactions::COUNTRY_CODE, '=', $transaction->getCountryCode());
        $select->where->add(Pap_Db_Table_Transactions::PAYOUT_STATUS, '=', $transaction->getPayoutStatus());
        $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, 'like', $this->dateTimeToDate($transaction->getDateInserted()).'%');
        $select->orderBy->add(Pap_Db_Table_Transactions::DATE_INSERTED, false);
        $select->limit->set(0, 1);

        $transaction = new Pap_Common_Transaction();
        $transaction->fillFromRecord($select->getOneRow());
        return $transaction;
    }

    /**
     * @return Pap_Common_Transcation
     */
    private function getCachedTransaction(Pap_Common_Transaction $transaction) {
        $hash = $this->hashTransaction($transaction);

        if (isset($this->transactions[$hash])) {
            return $this->transactions[$hash];
        }

        $transactionClone = $this->getClonedTransactionFromDb($transaction);
        $transactionClone->setPersistent(true);

        return $transactionClone;
    }



    private function hashTransaction(Pap_Common_Transaction $transaction) {
        return $transaction->getUserId(). $transaction->getCampaignId(). $transaction->getBannerId(). $transaction->getChannel() .
        $transaction->getStatus() . $transaction->getTier(). $transaction->getType(). $transaction->getPayoutStatus() . $this->dateTimeToDate($transaction->getDateInserted() . $transaction->getCountryCode());
    }

    private function dateTimeToDate($dateTime) {
        return substr($dateTime, 0, strpos($dateTime, ' '));
    }
}


?>
