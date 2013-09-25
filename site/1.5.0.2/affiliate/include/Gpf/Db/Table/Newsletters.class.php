<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.7
 *   $Id: Files.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_Newsletters extends Gpf_DbEngine_Table {
    const ID = 'newsletterid';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const SUCCESS_SIGNUP_URL = 'success_signup_url';
    const DOUBLE_OPTIN = 'double_optin';
    const OPTIN_MAILTEMPLATEID = 'optin_templateid';
    
    private static $instance;
        
    /**
     * @return Gpf_Db_Table_Newsletters
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('nl_newsletters');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::NAME, 'varchar', 255, true);
        $this->createColumn(self::DESCRIPTION, 'text', 0);
        $this->createColumn(self::SUCCESS_SIGNUP_URL, 'text', 0);
        $this->createColumn(self::DOUBLE_OPTIN, 'char', 1, true);
        $this->createColumn(Gpf_Db_Table_MailAccounts::ID, 'char', 8);
        $this->createColumn(self::OPTIN_MAILTEMPLATEID, 'char', 8, true);
        $this->createColumn(Gpf_Db_Table_Accounts::ID, 'char', 8, true);
    }
}
?>
