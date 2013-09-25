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
class Pap_Db_RecurringCommissionEntry extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Pap_Db_Table_RecurringCommissionEntries::getInstance());
        parent::init();
    }
    
    public function setRecurringCommissionId($id) {
        $this->set(Pap_Db_Table_RecurringCommissionEntries::RECURRING_COMMISSION_ID, $id);
    }
    
    public function setUserId($id) {
        $this->set(Pap_Db_Table_RecurringCommissionEntries::USERID, $id);
    }
    
    public function getUserId() {
        return $this->get(Pap_Db_Table_RecurringCommissionEntries::USERID);
    }
    
    public function setTier($tier) {
        $this->set(Pap_Db_Table_RecurringCommissionEntries::TIER, $tier);
    }
    
    public function getTier() {
        return $this->get(Pap_Db_Table_RecurringCommissionEntries::TIER);
    }
    
    public function setCommission($commission) {
        $this->set(Pap_Db_Table_RecurringCommissionEntries::COMMISSION, $commission);
    }
    
    public function getCommission() {
        return $this->get(Pap_Db_Table_RecurringCommissionEntries::COMMISSION);
    }
}
?>
