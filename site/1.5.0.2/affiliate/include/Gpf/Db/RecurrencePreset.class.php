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
class Gpf_Db_RecurrencePreset extends Gpf_DbEngine_Row {
    
    const USER_PRESET = 'U';
    const SYSTEM_PRESET = 'S';
    
    public function init() {
        $this->setTable(Gpf_Db_Table_RecurrencePresets::getInstance());
        parent::init();
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_RecurrencePresets::ACCOUNTID, $accountId);
    }
    
    public function setId($id) {
        $this->set(Gpf_Db_Table_RecurrencePresets::ID, $id);
    }
    
    public function setName($name) {
        $this->set(Gpf_Db_Table_RecurrencePresets::NAME, $name);
    }
    
    public function setType($type) {
        $this->set(Gpf_Db_Table_RecurrencePresets::TYPE, $type);
    }
    
    public function getId() {
        return $this->get(Gpf_Db_Table_RecurrencePresets::ID);
    }
    
    public function getType() {
        return $this->get(Gpf_Db_Table_RecurrencePresets::TYPE);
    }
    
    public function getStartDate() {
        return $this->get(Gpf_Db_Table_RecurrencePresets::STARTDATE);
    }
    
    public function getEndDate() {
        return $this->get(Gpf_Db_Table_RecurrencePresets::ENDDATE);
    }

}

?>
