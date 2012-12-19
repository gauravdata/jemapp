<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ActiveView.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_PasswordRequest extends Gpf_DbEngine_Row {
     
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_PasswordRequests::getInstance());
        parent::init();
    }

    function insert() {
        $this->set(Gpf_Db_Table_PasswordRequests::CREATED, Gpf_DbEngine_Database::getDateString());
        $this->setStatus(Gpf_Db_Table_PasswordRequests::STATUS_PENDING);
        parent::insert();
    }

    public function setStatus($status) {
        $this->set(Gpf_Db_Table_PasswordRequests::STATUS, $status);
    }
    
    public function getStatus() {
        return $this->get(Gpf_Db_Table_PasswordRequests::STATUS);
    }

    function setAuthUser($authId) {
        $this->set(Gpf_Db_Table_AuthUsers::ID, $authId);
    }

    function getAuthUser() {
        return $this->get(Gpf_Db_Table_AuthUsers::ID);
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_PasswordRequests::ID);
    }
    
    public function setId($requestId) {
        $this->set(Gpf_Db_Table_PasswordRequests::ID, $requestId);
    }
}
?>
