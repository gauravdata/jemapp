<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: AuthUsers.class.php 27074 2010-02-04 08:56:03Z mjancovic $
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
class Gpf_Db_Table_AuthUsers extends Gpf_DbEngine_Table {

    const ID = 'authid';
    const USERNAME = 'username';
    const PASSWORD = 'rpassword';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const AUTHTOKEN = 'authtoken';
    const NOTIFICATION_EMAIL = 'notificationemail';
    const IP = 'ip';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_authusers');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::USERNAME, self::CHAR, 60);
        $this->createColumn(self::PASSWORD, self::CHAR, 60);
        $this->createColumn(self::FIRSTNAME, self::CHAR, 100);
        $this->createColumn(self::LASTNAME, self::CHAR, 100);
        $this->createColumn(self::AUTHTOKEN, self::CHAR, 100);
        $this->createColumn(self::NOTIFICATION_EMAIL, self::CHAR, 80);
        $this->createColumn(self::IP, self::CHAR, 40);
    }

    protected function initConstraints() {
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_PasswordRequests::AUTHUSERID, new Gpf_Db_PasswordRequest());
        Gpf_Plugins_Engine::extensionPoint('AuthUsers.initConstraints', $this);
        $this->addConstraint(new Gpf_DbEngine_Row_PasswordConstraint(self::PASSWORD));        
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::USERNAME)));
    }
}

?>
