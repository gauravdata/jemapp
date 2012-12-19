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
class Pap_Stats_Computer_Graph_Transactions extends Pap_Stats_Computer_Graph_Base {

    private $dataType;
    private $type;
    private $commissionTypeId;
    private $tier;
    
    const COUNT = 'count(distinct(t.saleid))';
    const COMMISSION = 'sum(t.commission)';
    const TOTALCOST = 'sum(t.totalcost)';
    
    const ALL_TIERS = 'allTiers';
    
    public function __construct(Pap_Stats_Params $params, $dataType, $timeGroupBy) {
        parent::__construct(Pap_Db_Table_Transactions::getInstance(), $params, $timeGroupBy);
        $this->dataType = $dataType;
    }

    protected function initSelectClause() {
        parent::initSelectClause();
        $this->selectBuilder->select->add($this->dataType, "value");
    }
    
    public function setType($type) {
        $this->type = $type;
    }
    
    public function setTier($tier) {
        $this->tier = $tier;
    }
    
    public function setCommissionTypeId($typeId) {
        $this->commissionTypeId = $typeId;
    }
    
    protected function initWhereConditions() {
        parent::initWhereConditions();
        if ($this->type != null) {
            $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_TYPE, '=', $this->type);
        }
        if ($this->commissionTypeId != null) {
            $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::COMMISSIONTYPEID, '=', $this->commissionTypeId);
        }
        if ($this->tier == null) {
            $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::TIER, '=', '1');
        } else {
            if ($this->tier != self::ALL_TIERS) {
                $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::TIER, '=', $this->tier);
            }
        }
        if ($this->params->isStatusDefined()) {
            $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_STATUS, 'IN', explode(',', $this->params->getStatus()));
        }
    }
}
?>
