<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: Commission.class.php 22311 2008-11-14 12:36:10Z mjancovic $
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
class Pap_Db_RecurringCommission extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Pap_Db_Table_RecurringCommissions::getInstance());
        parent::init();
    }
    
    public function getId() {
        return $this->get(Pap_Db_Table_RecurringCommissions::ID);
    }
    
    public function setId($id) {
        $this->set(Pap_Db_Table_RecurringCommissions::ID, $id);
    }

    public function setTransactionId($transactionId) {
        $this->set(Pap_Db_Table_RecurringCommissions::TRANSACTION_ID, $transactionId);
    }
    
    public function getTransactionId() {
        return $this->get(Pap_Db_Table_RecurringCommissions::TRANSACTION_ID);
    }
    
    public function setRecurrencePresetId($recurrencePresetId) {
        $this->set(Pap_Db_Table_RecurringCommissions::RECURRENCE_PRESET_ID, $recurrencePresetId);
    }
    
    public function getRecurrencePresetId() {
        return $this->get(Pap_Db_Table_RecurringCommissions::RECURRENCE_PRESET_ID);
    }
    
    public function setCommissionTypeId($commissionTypeId) {
        $this->set(Pap_Db_Table_RecurringCommissions::COMMISSION_TYPE_ID, $commissionTypeId);
    }
    
    public function getCommissionTypeId() {
        return $this->get(Pap_Db_Table_RecurringCommissions::COMMISSION_TYPE_ID);
    }
    
    public function setStatus($status) {
        $this->set(Pap_Db_Table_RecurringCommissions::STATUS, $status);
    }
    
    public function getStatus() {
        return $this->get(Pap_Db_Table_RecurringCommissions::STATUS);
    }
    
    public function setLastCommissionDate($lastCommissionDate) {
        $this->set(Pap_Db_Table_RecurringCommissions::LAST_COMMISSION_DATE, $lastCommissionDate);
    }
    
    public function getLastCommissionDate() {
        return $this->get(Pap_Db_Table_RecurringCommissions::LAST_COMMISSION_DATE);
    }
}
?>
