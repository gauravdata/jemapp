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
class Pap_Features_PerformanceRewards_Action_AddBonusCommAction extends Pap_Features_PerformanceRewards_Action {
    /**
     * @throws Gpf_Exception
     */
    public function execute() {
        Gpf_Log::debug('Executing rule: Add bonus commission to affiliate...');
        if($this->isExtraBonusExists()) {
            Gpf_Log::debug('Bonus already exist. Skipping.');
            return;
        }
        $this->addBonus();
    }
    
    protected function addBonus() {
        if ($this->rule->getBonusType() == Pap_Features_PerformanceRewards_Rule_Transaction::BONUS_TYPE_AMOUNT) {
            $this->insertBonusTransaction($this->rule->getBonusValue());
        } else {
            $this->insertBonusTransaction($this->computePercentageValue());
        }
        $this->logMessage('Bonus commission added');
    }
    
    protected function insertBonusTransaction($commissionValue) {
        $transaction = new Pap_Common_Transaction();
        $transaction->setCommission($commissionValue);
        $transaction->setType(Pap_Db_Transaction::TYPE_EXTRA_BONUS);
        $transaction->setDateInserted(Gpf_Common_DateUtils::now());
        $transaction->setStatus('A');
        $transaction->setPayoutStatus('U');
        $transaction->setUserId($this->getCurrentUserId());
        $transaction->setCampaignId($this->transaction->getCampaignId());
        $transaction->setSystemNote('Commission of rule: ' . $this->rule->getString());
        $transaction->setData1($this->rule->getId());
        $transaction->setData2($this->getToDateRange());
        $transaction->insert();
    }
    
    private function getToDateRange() {
        return $this->rule->getDateRange()->getTo()->toDateTime();
    }

    protected function computePercentageValue() {
        $commissions = $this->rule->getCondition()->computeCommissions();
        return $commissions * $this->rule->getBonusValue() / 100;
    }

    protected function isExtraBonusExists() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('count(*)', 'cnt');
        $selectBuilder->from->add(Pap_Db_Table_Transactions::getName());

        $selectBuilder->where->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Db_Transaction::TYPE_EXTRA_BONUS);
        $selectBuilder->where->add(Pap_Db_Table_Transactions::USER_ID, '=', $this->getCurrentUserId());
        $selectBuilder->where->add(Pap_Db_Table_Transactions::CAMPAIGN_ID, '=', $this->transaction->getCampaignId());
        $selectBuilder->where->add(Pap_Db_Table_Transactions::DATA1, '=', $this->rule->getId());
        $selectBuilder->where->add(Pap_Db_Table_Transactions::DATA2, '=', $this->getToDateRange());

        $row = $selectBuilder->getOneRow();
        if ($row->get('cnt') > 0) {
            return true;
        }
        return false;
    }
    
    protected function getFormattedBonusValue() {
        return Pap_Common_Utils_CurrencyUtils::toStandardCurrencyFormat($this->rule->getBonusValue(), $this->rule->getBonusType());
    }

    public static function toString() {
        return Gpf_Lang::_("add bonus commission");
    }
    
    public function getString() {
        return sprintf(self::toString() . ' %s', $this->getFormattedBonusValue());
    }
}
?>
