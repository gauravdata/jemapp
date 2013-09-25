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
class Pap_Stats_Computer_Transactions extends Pap_Stats_Computer_Base {

    const FIRST_TIER = '1';
    const HIGHER_TIERS = 'T';
    const DEFAULT_COMMISSION_TYPEID = 'S';

    /**
     * @var array<Pap_Stats_Data_Commission>
     */
    private $resultCommissions = array();
    /**
     * @var array<Pap_Stats_Data_Commission>
     */
    private $resultTotalCost = array();
    /**
     * @var array<Pap_Stats_Data_Commission>
     */
    private $resultCount = array();

    private $transactionType = null;
    private $tier = null;
    private $commissionTypeId = null;

    public function __construct(Pap_Stats_Params $params) {
        parent::__construct(Pap_Db_Table_Transactions::getInstance(), $params);
    }

    public function setTransactionType($transactionType) {
        $this->transactionType = $transactionType;
    }

    public function setCommissionTypeId($commissionTypeId) {
        $this->commissionTypeId = $commissionTypeId;
    }

    public function setTier($tier) {
        $this->tier = $tier;
    }

    protected function initSelectClause() {
        $this->selectBuilder->select->add(Pap_Db_Table_Transactions::R_STATUS, "status", 't');
        $this->selectBuilder->select->add(Pap_Db_Table_Transactions::PAYOUT_STATUS, "payoutstatus", 't');
        $this->selectBuilder->select->add("sum(if(t.".Pap_Db_Table_Transactions::TIER."=1,1,0))", "cnt");
        $this->selectBuilder->select->add("sum(t.".Pap_Db_Table_Transactions::COMMISSION.")", "commission");
        $this->selectBuilder->select->add("sum(if(t.tier>1,0,t.".Pap_Db_Table_Transactions::TOTAL_COST."))", "totalcost");

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Stats_Computer_Transactions.initSelectClause', $this->selectBuilder->select);
    }

    protected function initGroupBy() {
        $this->selectBuilder->groupBy->add('t.'.Pap_Db_Table_Transactions::R_STATUS);
        $this->selectBuilder->groupBy->add('t.'.Pap_Db_Table_Transactions::PAYOUT_STATUS);

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Stats_Computer_Transactions.initGroupBy', $this->selectBuilder->groupBy);
    }

    protected function initWhereConditions() {
        parent::initWhereConditions();
        if ($this->transactionType != null) {
            $this->initTransactionTypeWhere($this->transactionType);
        }
        if ($this->commissionTypeId != null) {
            $this->initCommissionTypeIdWhere($this->commissionTypeId);
        }
        if ($this->tier != null) {
            if ($this->tier == self::HIGHER_TIERS) {
                $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::TIER, '>', 1);
            } else {
                $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::TIER, '=', $this->tier);
            }
        }
        if ($this->params->isStatusDefined()) {
            if (is_array($this->params->getStatus())) {
                $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_STATUS, 'IN', $this->params->getStatus());
            } else {
                $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_STATUS, 'IN', explode(',', $this->params->getStatus()));
            }
        }
        if ($this->params->isTypeDefined()) {
            if (is_array($this->params->getType())) {
                $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_TYPE, 'IN', $this->params->getType());
            } else {
                $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_TYPE, '=', $this->params->getType());
            }
        }
        if($this->params->getPayoutStatus() !== null) {
            $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::PAYOUT_STATUS, '=', $this->params->getPayoutStatus());
        }
    }

    protected function initCommissionTypeIdWhere($commissionTypeId) {
        $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::COMMISSIONTYPEID, '=', $commissionTypeId);
    }
    
    protected function initTransactionTypeWhere($transactionType) {
        if (is_array($transactionType)) {
            $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_TYPE, 'IN', $transactionType);
        } else {
            $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_TYPE, '=', $transactionType);
        }
    }

    protected function processResult() {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Stats_Computer_Transactions.processResult', $this->selectBuilder);
        
        $this->processAllRows($this->selectBuilder);
    }
    
    protected function processAllRows(Gpf_SqlBuilder_SelectBuilder $select) {
        foreach ($select->getAllRowsIterator() as $resultRow) {
            $this->processRow($resultRow);
        }
    }

    protected function processRow(Gpf_Data_Record $dataRow, $commissionTypeId = self::DEFAULT_COMMISSION_TYPEID) {
        if (!array_key_exists($commissionTypeId, $this->resultCommissions)) {
            $this->resultCommissions[$commissionTypeId] = new Pap_Stats_Data_Commission();
            $this->resultTotalCost[$commissionTypeId] = new Pap_Stats_Data_Commission();
            $this->resultCount[$commissionTypeId] = new Pap_Stats_Data_Commission();
        }
        
        $status = $dataRow->get("status");
        $commission = $dataRow->get("commission");
        $totalcost = $dataRow->get("totalcost");
        $count = $dataRow->get("cnt");

        if ($dataRow->get("payoutstatus") == Pap_Common_Constants::PSTATUS_PAID) {
            if ($status == Pap_Common_Constants::STATUS_APPROVED) {
                $this->resultCommissions[$commissionTypeId]->addPaid($commission);
                $this->resultTotalCost[$commissionTypeId]->addPaid($totalcost);
                $this->resultCount[$commissionTypeId]->addPaid($count);
            }
            return;
        }

        if ($status == Pap_Common_Constants::STATUS_APPROVED) {
            $this->resultCommissions[$commissionTypeId]->addApproved($commission);
            $this->resultTotalCost[$commissionTypeId]->addApproved($totalcost);
            $this->resultCount[$commissionTypeId]->addApproved($count);
            return;
        }
        if ($status == Pap_Common_Constants::STATUS_PENDING) {
            $this->resultCommissions[$commissionTypeId]->addPending($commission);
            $this->resultTotalCost[$commissionTypeId]->addPending($totalcost);
            $this->resultCount[$commissionTypeId]->addPending($count);
            return;
        }
        $this->resultCommissions[$commissionTypeId]->addDeclined($commission);
        $this->resultTotalCost[$commissionTypeId]->addDeclined($totalcost);
        $this->resultCount[$commissionTypeId]->addDeclined($count);
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCommissions($commissionTypeId = self::DEFAULT_COMMISSION_TYPEID) {
        if (!array_key_exists($commissionTypeId, $this->resultCommissions)) {
            $this->resultCommissions[$commissionTypeId] = new Pap_Stats_Data_Commission();
        }
        return $this->resultCommissions[$commissionTypeId];
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getTotalCost($commissionTypeId = self::DEFAULT_COMMISSION_TYPEID) {
        if (!array_key_exists($commissionTypeId, $this->resultTotalCost)) {
            $this->resultTotalCost[$commissionTypeId] = new Pap_Stats_Data_Commission();
        }
        return $this->resultTotalCost[$commissionTypeId];
    }

    /**
     * @return Pap_Stats_Data_Commission
     */
    public function getCount($commissionTypeId = self::DEFAULT_COMMISSION_TYPEID) {
        if (!array_key_exists($commissionTypeId, $this->resultCount)) {
            $this->resultCount[$commissionTypeId] = new Pap_Stats_Data_Commission();
        }
        return $this->resultCount[$commissionTypeId];
    }
}
?>
