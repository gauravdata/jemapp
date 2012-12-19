<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Db_PlannedTask extends Gpf_DbEngine_Row {

    public function init() {
        $this->setTable(Gpf_Db_Table_PlannedTasks::getInstance());
        parent::init();
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_PlannedTasks::ACCOUNTID, $accountId);
    }
    
    public function setRecurrencePresetId($id) {
        $this->set(Gpf_Db_Table_PlannedTasks::RECURRENCEPRESETID, $id);
    }
    
    public function getRecurrencePresetId() {
        return $this->get(Gpf_Db_Table_PlannedTasks::RECURRENCEPRESETID);
    }
    
    public function setLastPlanDate($lastPlanDate) {
        $this->set(Gpf_Db_Table_PlannedTasks::LASTPLANDATE, $lastPlanDate);
    }
    
    public function getLastPlanDate() {
        return $this->get(Gpf_Db_Table_PlannedTasks::LASTPLANDATE);
    }
    
    public function getClassName() {
        return $this->get(Gpf_Db_Table_PlannedTasks::CLASSNAME);
    }
    
    public function setClassName($classname) {
        $this->set(Gpf_Db_Table_PlannedTasks::CLASSNAME, $classname);
    }
    
    public function getParams() {
        return $this->get(Gpf_Db_Table_PlannedTasks::PARAMS);
    }
    
    public function getAccountId() {
    	return $this->get(Gpf_Db_Table_PlannedTasks::ACCOUNTID);
    }
    
    public function setParams($value) {
        $this->set(Gpf_Db_Table_PlannedTasks::PARAMS, $value);
    }
}

?>
