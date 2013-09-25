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
abstract class Pap_Features_PerformanceRewards_Condition_Stat extends Pap_Features_PerformanceRewards_Condition {
    /**
     *
     * @var Pap_Stats_Params
     */
    protected $params;

    /**
     *
     * @var Pap_Stats_Transactions
     */
    private $statComputer;

    public function __construct(Pap_Features_PerformanceRewards_Rule_Transaction $rule) {
        parent::__construct($rule);
        $this->params = new Pap_Stats_Params();
        
    }
    
    protected function createComputer() {
        return new Pap_Stats_TransactionsFirstTier($this->params);
    }
    
    protected function prepareParams() {
        $this->params->setStatus($this->getStatuses());
        $this->params->setAffiliateId($this->rule->getUserID());
        $this->params->setCampaignId($this->rule->getCampaignId());

        if (!$this->rule->getDateRange()->isAllTime()) {
            $this->params->setRange($this->rule->getDateRange());
        }
        if ($this->rule->getDate() == Pap_Features_PerformanceRewards_Rule::DATE_ALL_UNPAID_COMMISSIONS) {
            $this->params->setPayoutStatus(Pap_Common_Constants::PSTATUS_UNPAID);
        }
    }
    
    protected function getStatComputer() {
        if($this->statComputer === null) {
            $this->statComputer = $this->createComputer();
            $this->prepareParams();
        }
        return $this->statComputer;
    }
    
    public function computeCommissions() {
        return $this->getStatComputer()->getCommission()->getAll();
    }
}
?>
