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
class Gpf_Db_RecurrenceSetting extends Gpf_DbEngine_Row {

    const TYPE_ONCE  = 'O';
    const TYPE_EACH  = 'E';
    const TYPE_HOUR  = 'H';
    const TYPE_DAY   = 'D';
    const TYPE_WEEK  = 'W';
    const TYPE_MONTH = 'M';
    const TYPE_YEAR  = 'Y';
    
    const NO_PERIOD = -1;
    
    public function init() {
        $this->setTable(Gpf_Db_Table_RecurrenceSettings::getInstance());
        parent::init();
    }
    
    public function setRecurrencePresetId($id) {
        $this->set(Gpf_Db_Table_RecurrenceSettings::RECURRENCEPRESETID, $id);
    }
    
    public function setId($id) {
        $this->set(Gpf_Db_Table_RecurrenceSettings::ID, $id); 
    }
    
    public function setType($type) {
        $this->set(Gpf_Db_Table_RecurrenceSettings::TYPE, $type);
    }
    
    public function setPeriod($period) {
        $this->set(Gpf_Db_Table_RecurrenceSettings::PERIOD, $period);
    }
    
    public function setFrequency($frequency) {
        $this->set(Gpf_Db_Table_RecurrenceSettings::FREQUENCY, $frequency);
    }
    
    public function getType() {
        return $this->get(Gpf_Db_Table_RecurrenceSettings::TYPE);
    }
    
    public function getPeriod() {
        return $this->get(Gpf_Db_Table_RecurrenceSettings::PERIOD);
    }
    
    public function getFrequency() {
        return $this->get(Gpf_Db_Table_RecurrenceSettings::FREQUENCY);
    }

}

?>
