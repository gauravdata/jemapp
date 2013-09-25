<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Galik
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
class Pap_Db_CommissionTypeAttribute extends Gpf_DbEngine_Row {
    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Pap_Db_Table_CommissionTypeAttributes::getInstance());
        parent::init();
    }

    public function getId() {
    	return $this->get(Pap_Db_Table_CommissionTypeAttributes::ID);
    }
    
    public function setId($commissionTypeAttributeId) {
    	$this->set(Pap_Db_Table_CommissionTypeAttributes::ID, $commissionTypeAttributeId);
    }

    public function getCommissionTypeId() {
        return $this->get(Pap_Db_Table_CommissionTypeAttributes::COMMISSION_TYPE_ID);
    }
    
    public function setCommissionTypeId($commissionTypeId) {
        $this->set(Pap_Db_Table_CommissionTypeAttributes::COMMISSION_TYPE_ID, $commissionTypeId);
    }
        
    public function getName() {
        return $this->get(Pap_Db_Table_CommissionTypeAttributes::NAME);
    }

    public function setName($name) {
        $this->set(Pap_Db_Table_CommissionTypeAttributes::NAME, $name);
    }
    
    public function getValue() {
        return $this->get(Pap_Db_Table_CommissionTypeAttributes::VALUE);
    }
   
    public function setValue($value) {
        $this->set(Pap_Db_Table_CommissionTypeAttributes::VALUE, $value);
    }
}

?>
