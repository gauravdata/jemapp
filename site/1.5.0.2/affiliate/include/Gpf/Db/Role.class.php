<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Role.class.php 22375 2008-11-19 13:11:38Z vzeman $
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
class Gpf_Db_Role extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Roles::getInstance());
        parent::init();
    }

    public function setRoleType($type) {
        $this->set(Gpf_Db_Table_Roles::TYPE, $type);
    }

    public function getRoleType() {
        return $this->get(Gpf_Db_Table_Roles::TYPE);
    }

    public function setName($name) {
        $this->set(Gpf_Db_Table_Roles::NAME, $name);
    }

    public function getName() {
        return $this->get(Gpf_Db_Table_Roles::NAME);
    }
    
    public function setId($id) {
        $this->set(Gpf_Db_Table_Roles::ID, $id);
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_Roles::ID);
    }

    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_Accounts::ID, $accountId);
    }

    public function getAccountId() {
        return $this->get(Gpf_Db_Table_Accounts::ID);
    }

    public function delete() {
        $this->load();
        if (!strlen($this->getAccountId())) {
            throw new Gpf_Exception($this->_('Default role can not be deleted!'));
        }
        return parent::delete();
    }
}

?>
