<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailAccounts.class.php 26006 2009-11-05 08:51:36Z vzeman $
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
class Gpf_Db_Table_MailAccounts extends Gpf_DbEngine_Table {

    const ID = 'mailaccountid';
    const ACCOUNT_NAME = 'account_name';
    const ACCOUNT_EMAIL = 'account_email';
    const FROM_NAME = 'from_name';
    const POP3_SERVER = 'pop3_server';
    const POP3_PORT = 'pop3_port';
    const POP3_SSL = 'pop3_ssl';
    const POP3_USERNAME = 'pop3_username';
    const POP3_PASSWORD = 'pop3_password';
    const USE_SMTP = 'use_smtp';
    const SMTP_SERVER = 'smtp_server';
    const SMTP_PORT = 'smtp_port';
    const SMTP_SSL = 'smtp_ssl';
    const SMTP_AUTH = 'smtp_auth';
    const SMTP_USERNAME = 'smtp_username';
    const SMTP_PASSWORD = 'smtp_password';
    const SMTP_AUTH_METHOD = 'smtp_auth_method';

    const DELETE_MAILS = 'delete_mails';
    const LAST_UNIQUE_ID = 'last_unique_id';
    const IS_DEFAULT = 'is_default';

    const LAST_MAIL_DATETIME = 'last_mail_datetime';
    const LASTT_PROCESING = 'last_processing';
    const ACCOUNT_ID = 'accountid';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_mail_accounts');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::ACCOUNT_NAME, 'char', 255);
        $this->createColumn(self::ACCOUNT_EMAIL, 'char', 255);

        $this->createColumn(self::FROM_NAME, 'char', 255);

        $this->createColumn(self::POP3_SERVER, 'char', 255);
        $this->createColumn(self::POP3_PORT, 'int');
        $this->createColumn(self::POP3_SSL, 'char', 1);
        $this->createColumn(self::POP3_USERNAME, 'char', 255);
        $this->createColumn(self::POP3_PASSWORD, 'char', 255);

        $this->createColumn(self::USE_SMTP, 'char', 1);
        $this->createColumn(self::SMTP_SERVER, 'char', 255);
        $this->createColumn(self::SMTP_PORT, 'int');
        $this->createColumn(self::SMTP_SSL, 'char', 1);
        $this->createColumn(self::SMTP_AUTH, 'char', 1);
        $this->createColumn(self::SMTP_USERNAME, 'char', 255);
        $this->createColumn(self::SMTP_PASSWORD, 'char', 255);
        $this->createColumn(self::SMTP_AUTH_METHOD, 'char', 16, false);


        $this->createColumn(self::DELETE_MAILS, 'char', 1);
        $this->createColumn(self::LAST_UNIQUE_ID, 'char', 255);
        $this->createColumn(self::IS_DEFAULT, 'char', 1);

        $this->createColumn(self::LAST_MAIL_DATETIME, 'datetime');
        $this->createColumn(self::LASTT_PROCESING, 'datetime');

        $this->createColumn(self::ACCOUNT_ID, 'char', 8);
    }

    /**
     * Return default mail account from database
     *
     * @return Gpf_Db_MailAccount
     */
    public function getDefaultMailAccount() {
        $mailAccount = new Gpf_Db_MailAccount();
        $mailAccount->set(self::IS_DEFAULT, Gpf::YES);
        try {
            $mailAccount->loadFromData(array(self::IS_DEFAULT));
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception($this->_("Failed to load default mail account. Please check your mail account settings."));
        }
        return $mailAccount;
    }
}
?>
