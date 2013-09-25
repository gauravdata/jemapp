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
class Pap_Stats_Computer_RefundChargeBack extends Pap_Stats_Computer_Transactions {
  
    private $mainType;
    
    public function __construct(Pap_Stats_Params $params, $mainType) {
        parent::__construct($params);
        $this->mainType = $mainType;
    }
    
    protected function initFrom() {
        parent::initFrom();
        $this->selectBuilder->from->addLeftJoin(Pap_Db_Table_Transactions::getName(), 'p',
            'p.'.Pap_Db_Table_Transactions::TRANSACTION_ID.'=t.'.Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID);
    }
    
    protected function initWhereConditions() {
        parent::initWhereConditions();
        $this->selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::R_TYPE, '=', $this->mainType);
    }
    
    protected function initCommissionTypeIdWhere($commissionTypeId) {
        $this->selectBuilder->where->add('p.'.Pap_Db_Table_Transactions::COMMISSIONTYPEID, '=', $commissionTypeId);
    }
    
    protected function initTransactionTypeWhere($transactionType) {
        if (is_array($transactionType)) {
            $this->selectBuilder->where->add('p.'.Pap_Db_Table_Transactions::R_TYPE, 'IN', $transactionType);
        } else {
            $this->selectBuilder->where->add('p.'.Pap_Db_Table_Transactions::R_TYPE, '=', $transactionType);
        }
    }
}
?>
