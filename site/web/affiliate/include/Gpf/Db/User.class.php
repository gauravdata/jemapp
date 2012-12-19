<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: User.class.php 27074 2010-02-04 08:56:03Z mjancovic $
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
class Gpf_Db_User extends Gpf_DbEngine_Row {
    const APPROVED = 'A';
    const PENDING  = 'P';
    const DECLINED = 'D';

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Users::getInstance());
        parent::init();
    }
    
    public function getId() {
        return $this->get(Gpf_Db_Table_Users::ID);
    }
    
    public function getAuthId() {
        return $this->get(Gpf_Db_Table_Users::AUTHID);
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_Users::ACCOUNTID, $accountId);
    }
    
    public function getAccountId() {
        return $this->get(Gpf_Db_Table_Accounts::ID);
    }
    
    public function setId($id) {
        $this->set(Gpf_Db_Table_Users::ID, $id);
    }
    
    public function setRoleId($roleId) {
        $this->set(Gpf_Db_Table_Users::ROLEID, $roleId);
    }
    
    public function getRoleId() {
        return $this->get(Gpf_Db_Table_Users::ROLEID);
    }
    
    public function setStatus($newStatus) {
        $this->set(Gpf_Db_Table_Users::STATUS, $newStatus);
    }
    
    public function getStatus() {
        return $this->get(Gpf_Db_Table_Users::STATUS);
    }
    
    public function setAuthId($authId) {
        $this->set(Gpf_Db_Table_Users::AUTHID, $authId);
    }
    
    public function loadByRoleType($roleType, $application) {
        $query = new Gpf_SqlBuilder_SelectBuilder();
        $query->select->addAll(Gpf_Db_Table_Users::getInstance(), 'u');
        $query->from->add(Gpf_Db_Table_Users::getName(), "u");
        $query->from->addInnerJoin(Gpf_Db_Table_Roles::getName(), "r", "r.roleid = u.roleid");
        $query->from->addInnerJoin(Gpf_Db_Table_Accounts::getName(), "a", "u.accountid = a.accountid");
        $query->where->add('u.authid', '=', $this->getAuthId());
        $query->where->add('u.accountid', '=', $this->getAccountId());
        $query->where->add('a.application', '=', $application);
        $query->where->add('r.roletype', '=', $roleType);
        $record = $query->getOneRow();
        $this->fillFromRecord($record);
    }
    
    public function isStatusValid() {
        $status = $this->getStatus();
        return in_array($status, array(self::APPROVED, self::PENDING, self::DECLINED));
    }
    
    protected function beforeSaveCheck() {
    	parent::beforeSaveCheck();
        if(!$this->isStatusValid()) {
            throw new Gpf_Exception('User status is invalid.');
        }
    }
}

?>
