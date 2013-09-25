<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RolePrivileges.class.php 29662 2010-10-25 08:32:20Z mbebjak $
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
class Gpf_Db_Table_RolePrivileges extends Gpf_DbEngine_Table {
    const ID = "roleprivilegeid";
    const ROLE_ID = "roleid";
    const OBJECT = "object";
    const PRIVILEGE = "privilege";
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_rolesprivileges');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::INT, 0, true);
        $this->createColumn(self::ROLE_ID, self::CHAR, 8);
        $this->createColumn(self::OBJECT, self::CHAR, 40);
        $this->createColumn(self::PRIVILEGE, self::CHAR, 40);
    }
    
    /**
     * @param string $roleId
     * @return Gpf_Data_RecordSet
     */
    public function getAllPrivileges($roleId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        
        $select->select->add(self::ID, 'id');
        $select->select->add(self::OBJECT, 'object');
        $select->select->add(self::PRIVILEGE, 'privilege');
        
        $select->from->add(self::getName());
        $select->where->add(self::ROLE_ID, "=", $roleId);
        
        $privileges = new Gpf_Data_RecordSet();
        $privileges->load($select);
        return $privileges;
    }
    
    /**
     * Delete all privileges for current role
     *
     * @param string $roleId
     */
    public function deleteAllPrivileges($roleId) {
        $select = new Gpf_SqlBuilder_DeleteBuilder();
        
        $select->from->add(self::getName());
        $select->where->add(self::ROLE_ID, "=", $roleId);
        
        $select->delete();
    }
    
    
}
