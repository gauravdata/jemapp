<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Gpf_Db_QuickTask extends Gpf_DbEngine_Row {

    public function init() {
        $this->setTable(Gpf_Db_Table_QuickTasks::getInstance());
        parent::init();
    }

    public function setId($id) {
        $this->set(Gpf_Db_Table_QuickTasks::ID, $id);
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_QuickTasks::ACCOUNTID, $accountId);
    }
    
    public function setGroupId($groupId = '') {
        $this->set(Gpf_Db_Table_QuickTasks::GROUPID, $groupId);
    }
    
    public function setRequest($request) {
        $this->set(Gpf_Db_Table_QuickTasks::REQUEST, $request);
    }

    public function setValidTo($validTo) {
        $this->set(Gpf_Db_Table_QuickTasks::VALIDTO, $validTo);
    }
    
    public function getId() {
        return $this->get(Gpf_Db_Table_QuickTasks::ID);    
    }
    
    public function getAccountId() {
        return $this->get(Gpf_Db_Table_QuickTasks::ACCOUNTID);
    }
    
    public function getGroupId() {
        return $this->get(Gpf_Db_Table_QuickTasks::GROUPID);
    }
    
    public function getRequest() {
        return $this->get(Gpf_Db_Table_QuickTasks::REQUEST);
    }

    public function isValid() {
        $validTo = new Gpf_DateTime($this->get(Gpf_Db_Table_QuickTasks::VALIDTO));
        return $validTo->compare(new Gpf_DateTime()) > 0;
    }
}

?>
