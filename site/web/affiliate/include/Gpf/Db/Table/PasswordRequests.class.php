<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Accounts.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_PasswordRequests extends Gpf_DbEngine_Table {
    const ID = 'requestid';
    const CREATED = 'created';
    const STATUS = 'status';
    const AUTHUSERID = 'authid';
    
    const STATUS_PENDING = 'p';
    const STATUS_EXPIRED = 'e';
    const STATUS_INVALIDATED = 'i';
    const STATUS_APPLIED = 'a';
    
    const VALID_SECONDS = 86400;
    
    private static $instance;
    
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function initName() {
        $this->setName('g_passwd_requests');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::CREATED, 'datetime');
        $this->createColumn(self::AUTHUSERID, 'char', 8);
        $this->createColumn(self::STATUS, 'char', 1);
    }
    
    public static function expireOldRequest() {
        $sql = new Gpf_SqlBuilder_UpdateBuilder();
        $sql->set->add(self::STATUS, self::STATUS_EXPIRED);
        $sql->from->add(self::getName());
        $sql->where->add(self::STATUS, '=', self::STATUS_PENDING);
        $sql->where->add(self::CREATED, '<', Gpf_DbEngine_Database::getDateString(time()-self::VALID_SECONDS));
        $sql->execute();
    }
    
    public static function invalidateOtherRequest($authId) {
        $sql = new Gpf_SqlBuilder_UpdateBuilder();
        $sql->set->add(self::STATUS, self::STATUS_INVALIDATED);
        $sql->from->add(self::getName());
        $sql->where->add(self::STATUS, '=', self::STATUS_PENDING);
        $sql->where->add(Gpf_Db_Table_AuthUsers::ID, '=', $authId);
        $sql->execute();
    }
    
    
}
?>
