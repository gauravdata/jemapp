<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Accounts.class.php 19734 2008-08-08 07:36:13Z mbebjak $
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
class Gpf_Db_Table_LoginsHistory extends Gpf_DbEngine_Table {
    const ID = 'loginid';
    const LOGIN = 'login';
    const LOGOUT = 'logout';
    const LAST_REQUEST = 'lastrequest';
    const IP = 'ip';
    const ACCOUNTUSERID = 'accountuserid';
    
    private static $instance;
    
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function initName() {
        $this->setName('g_logins');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'int', 0, true);
        $this->createColumn(self::ACCOUNTUSERID, 'char', 8);
        $this->createColumn(self::LOGIN, 'datetime');
        $this->createColumn(self::LOGOUT, 'datetime');
        $this->createColumn(self::LAST_REQUEST, 'datetime');
        $this->createColumn(self::IP, 'char', 39);
    }
}

?>
