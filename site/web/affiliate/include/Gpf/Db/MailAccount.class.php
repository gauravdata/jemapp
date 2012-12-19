<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailAccount.class.php 26830 2010-01-13 16:06:29Z mbebjak $
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
class Gpf_Db_MailAccount extends Gpf_DbEngine_Row {
	const DEFAULT_POP3_PORT = 110;
	const DEFAULT_SMTP_PORT = 25;

	function __construct(){
		parent::__construct();
		$this->set(Gpf_Db_Table_MailAccounts::POP3_PORT, self::DEFAULT_POP3_PORT);
		$this->set(Gpf_Db_Table_MailAccounts::SMTP_PORT, self::DEFAULT_SMTP_PORT);
		$this->set(Gpf_Db_Table_MailAccounts::USE_SMTP, Gpf::NO);
        $this->set(Gpf_Db_Table_MailAccounts::SMTP_AUTH, Gpf::NO);
        $this->set(Gpf_Db_Table_MailAccounts::SMTP_SSL, Gpf::NO);
	}

	function init() {
		$this->setTable(Gpf_Db_Table_MailAccounts::getInstance());
		parent::init();
	}

	protected function beforeSaveAction() {
		if ($this->get(Gpf_Db_Table_MailAccounts::IS_DEFAULT) == Gpf::YES) {
			$update = new Gpf_SqlBuilder_UpdateBuilder();
			$update->from->add(Gpf_Db_Table_MailAccounts::getName());
			$update->set->add(Gpf_Db_Table_MailAccounts::IS_DEFAULT, Gpf::NO);
			$update->where->add(Gpf_Db_Table_MailAccounts::ID, '<>', $this->getPrimaryKeyValue());
			$update->execute();
		}
	}

	public function isDefault() {
	    return $this->get(Gpf_Db_Table_MailAccounts::IS_DEFAULT) == Gpf::YES;
	}

    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_MailAccounts::ACCOUNT_ID, $accountId);
    }
	
	public function setAccountName($accountName) {
	    $this->set(Gpf_Db_Table_MailAccounts::ACCOUNT_NAME, $accountName);
	}

	public function getAccountName() {
	    return $this->get(Gpf_Db_Table_MailAccounts::ACCOUNT_NAME);
	}

    public function setAccountEmail($email) {
        $this->set(Gpf_Db_Table_MailAccounts::ACCOUNT_EMAIL, $email);
    }

    public function setFromName($name) {
        $this->set(Gpf_Db_Table_MailAccounts::FROM_NAME, $name);
    }

	public function setId($id) {
	    $this->set(Gpf_Db_Table_MailAccounts::ID, $id);
	}

    public function getId() {
        return $this->get(Gpf_Db_Table_MailAccounts::ID);
    }

	public function getAccountEmail() {
	    return $this->get(Gpf_Db_Table_MailAccounts::ACCOUNT_EMAIL);
	}

	public function getFromName() {
	    return $this->get(Gpf_Db_Table_MailAccounts::FROM_NAME);
	}

	public function setAsDefault($isDefault = true) {
	    if ($isDefault) {
	       $this->set(Gpf_Db_Table_MailAccounts::IS_DEFAULT, Gpf::YES);
	    } else {
           $this->set(Gpf_Db_Table_MailAccounts::IS_DEFAULT, Gpf::NO);
	    }
	}

	public function setSmtpServer($server) {
	    $this->set(Gpf_Db_Table_MailAccounts::SMTP_SERVER, $server);
	}

	public function getSmtpServer() {
        return $this->get(Gpf_Db_Table_MailAccounts::SMTP_SERVER);
    }

	public function setSmtpUser($username) {
	    $this->set(Gpf_Db_Table_MailAccounts::SMTP_USERNAME, $username);
	}

    public function getSmtpUser() {
        return $this->get(Gpf_Db_Table_MailAccounts::SMTP_USERNAME);
    }

    public function setSmtpPassword($password) {
        $this->set(Gpf_Db_Table_MailAccounts::SMTP_PASSWORD, $password);
    }

    public function setSmtpUseAthentication($useAuth) {
        if ($useAuth || $useAuth == Gpf::YES) {
            $this->set(Gpf_Db_Table_MailAccounts::SMTP_AUTH, Gpf::YES);
        } else {
            $this->set(Gpf_Db_Table_MailAccounts::SMTP_AUTH, Gpf::NO);
        }
    }

    public function useSmtpAuth() {
        return $this->get(Gpf_Db_Table_MailAccounts::SMTP_AUTH);
    }

    public function setUseSmtp($useSmtp) {
        if ($useSmtp) {
            $this->set(Gpf_Db_Table_MailAccounts::USE_SMTP, Gpf::YES);
        } else {
            $this->set(Gpf_Db_Table_MailAccounts::USE_SMTP, Gpf::NO);
        }
    }

    public function useSmtp() {
        return $this->get(Gpf_Db_Table_MailAccounts::USE_SMTP) == Gpf::YES;
    }

    public function useSmtpSsl() {
        return $this->get(Gpf_Db_Table_MailAccounts::SMTP_SSL) == Gpf::YES;
    }

    public function setSmtpPort($port) {
        $this->set(Gpf_Db_Table_MailAccounts::SMTP_PORT, $port);
    }

    public function getSmtpPort() {
        return $this->get(Gpf_Db_Table_MailAccounts::SMTP_PORT);
    }

    public function setSmtpAuthMethod($method) {
        $this->set(Gpf_Db_Table_MailAccounts::SMTP_AUTH_METHOD, $method);
    }

    public function getSmtpAuthMethod() {
        return $this->get(Gpf_Db_Table_MailAccounts::SMTP_AUTH_METHOD);
    }
}
