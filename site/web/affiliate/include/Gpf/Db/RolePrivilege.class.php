<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Role.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_RolePrivilege extends Gpf_DbEngine_Row {
    
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_RolePrivileges::getInstance());
        parent::init();
    }
    
    public function setObject($object) {
        $this->set(Gpf_Db_Table_RolePrivileges::OBJECT, $object);
    }
    
    public function setPrivilege($privilege) {
        $this->set(Gpf_Db_Table_RolePrivileges::PRIVILEGE, $privilege);
    }
    
    public function setRoleId($id) {
        $this->set(Gpf_Db_Table_RolePrivileges::ROLE_ID, $id);
    }
}

?>
