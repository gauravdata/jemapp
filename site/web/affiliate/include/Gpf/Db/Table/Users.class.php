<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Users.class.php 27074 2010-02-04 08:56:03Z mjancovic $
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
class Gpf_Db_Table_Users extends Gpf_DbEngine_Table {

    /**
     * @deprecated use const ID instead
     */
    public static $ID = 'accountuserid';
    /**
     * @deprecated use const ACCOUNTID instead
     */
    public static $ACCOUNTID = 'accountid';
    
    const ID = 'accountuserid';
    const AUTHID = 'authid';
    const ACCOUNTID = 'accountid';
    const ROLEID = 'roleid';
    const STATUS = 'rstatus';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_users');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::AUTHID, 'char', 100);
        $this->createColumn(self::ACCOUNTID, 'char', 20);
        $this->createColumn(self::ROLEID, 'char', 20);
        $this->createColumn(self::STATUS, 'char', 1);
    }
    
    protected function initConstraints() {
       $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, new Gpf_Db_UserAttribute());
       $this->addDeleteConstraint(new Gpf_Db_Table_Users_AuthUsersDeleteConstraint(self::AUTHID, Gpf_Db_Table_AuthUsers::ID, new Gpf_Db_AuthUser()));
       
       $this->addConstraint(new Gpf_Db_Table_Constraints_UsersUniqueConstraint());
    }
}

class Gpf_Db_Table_Users_AuthUsersDeleteConstraint extends Gpf_DbEngine_CascadeDeleteConstraint {
   
    public function execute(Gpf_DbEngine_Row $dbRow) {
        if (!$this->isLastUserWithAuthID($dbRow->get(Gpf_Db_Table_Users::AUTHID))) {
            return;
        }
        parent::execute($dbRow);
    } 
       
    /**
     * @param $authId
     * @return boolean
     */
    private function isLastUserWithAuthID($authId) {
        $guser = new Gpf_Db_User();
        $guser->setAuthId($authId);
        try {
            $guser->loadFromData(array(Gpf_Db_Table_Users::AUTHID));
        } catch (Gpf_Exception $e) {
            return false;
        } 
        return true;
    }
}
?>
