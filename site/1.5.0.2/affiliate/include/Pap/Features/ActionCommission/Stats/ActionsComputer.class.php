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
class Pap_Features_ActionCommission_Stats_ActionsComputer extends Pap_Stats_Computer_Transactions {
    
    const ACTIONS_COMMISSIONTYPEID = 'commissiontypeid';

    private $isComputed = false;
    
    private $commissionTypeNames = array();
    
    public function __construct(Pap_Stats_Params $params) {
        parent::__construct($params);
    }
    
    public function isComputed() {
        return $this->isComputed;
    }
    
    protected function initSelectClause() {
        parent::initSelectClause();
        $this->selectBuilder->select->add(Pap_Db_Table_Transactions::COMMISSIONTYPEID, self::ACTIONS_COMMISSIONTYPEID, 't');
    }    
    
    protected function initWhereConditions() {
        parent::initWhereConditions();          
        $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_TYPE, 'NOT IN', array(Pap_Common_Constants::TYPE_CHARGEBACK, Pap_Common_Constants::TYPE_REFUND));   
    }

    protected function initGroupBy() {
        parent::initGroupBy();
        $this->selectBuilder->groupBy->add('t.'.Pap_Db_Table_Transactions::COMMISSIONTYPEID);
    }
    
    
    protected function processAllRows(Gpf_SqlBuilder_SelectBuilder $select) {
        $this->isComputed = true;
        if (!$select->select->existsAlias(self::ACTIONS_COMMISSIONTYPEID)) {
            $select->select->add('s.'.self::ACTIONS_COMMISSIONTYPEID);
            $select->groupBy->add('s.'.self::ACTIONS_COMMISSIONTYPEID);
        }
        foreach ($select->getAllRowsIterator() as $resultRow) {
            $this->processRow($resultRow, $resultRow->get(self::ACTIONS_COMMISSIONTYPEID));
        }
    }
    
    public function addCommissionTypeName($commissionTypeId, $name) {
        $this->commissionTypeNames[$commissionTypeId] = $name;
    }
    
    public function getName($commissionTypeId) {
        return $this->commissionTypeNames[$commissionTypeId];
    }
}
?>
