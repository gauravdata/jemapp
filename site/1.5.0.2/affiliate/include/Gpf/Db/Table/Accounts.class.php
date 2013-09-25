<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Accounts.class.php 28865 2010-07-21 08:24:14Z iivanco $
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
class Gpf_Db_Table_Accounts extends Gpf_DbEngine_Table {
    const ID = 'accountid';
    const NAME = 'name';
    const STATUS = 'rstatus';
    const EMAIL = 'email';
    const APPLICATION = 'application';
    const DATEINSERTED = 'dateinserted';
    const AGREEMENT = 'agreement';
    const ACCOUNT_NOTE = 'accountnote';
    const SYSTEM_NOTE = 'systemnote';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_accounts');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    public static function getDataColumnName($i) {
        return 'data'.$i;
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::NAME, self::CHAR, 80);
        $this->createColumn(self::STATUS, self::CHAR, 1);
        $this->createColumn(self::APPLICATION, self::CHAR, 20);
        $this->createColumn(self::EMAIL, self::CHAR, 255);
        $this->createColumn(self::DATEINSERTED, self::DATETIME);
        $this->createColumn(self::AGREEMENT, self::CHAR);
        $this->createColumn(self::ACCOUNT_NOTE, self::CHAR);
        $this->createColumn(self::SYSTEM_NOTE, self::CHAR);
        for ($i = 1; $i <= 25; $i++) {
            $this->createColumn(self::getDataColumnName($i), self::CHAR, 255);
        }
    }

    protected function initConstraints() {
    	$this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::EMAIL), $this->_('Email must be unique!')));
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_Currencies::ACCOUNTID, new Gpf_Db_Currency());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_Exports::ACCOUNT_ID, new Gpf_Db_Export());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_FieldGroups::ACCOUNTID, new Gpf_Db_FieldGroup());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_FormFields::ACCOUNTID, new Gpf_Db_FormField());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_ImportExports::ACCOUNT_ID, new Gpf_Db_ImportExport());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_Languages::ACCOUNTID, new Gpf_Db_Language());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_MailAccounts::ACCOUNT_ID, new Gpf_Db_MailAccount());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_MailTemplates::ACCOUNT_ID, new Gpf_Db_MailTemplate());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_Settings::ACCOUNTID, new Gpf_Db_Setting());
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_Users::ACCOUNTID, new Gpf_Db_User());
        Gpf_Plugins_Engine::extensionPoint('Accounts.initConstraints', $this);
    }

    /**
     *
     * @param Gpf_Auth_Info $authInfo
     * @return Gpf_Data_RecordSet
     */
    public static function getAccounts(Gpf_Auth_Info $authInfo) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add('a.accountid', 'accountid');
        $select->select->add('a.name', 'name');

        $select->from->add(Gpf_Db_Table_AuthUsers::getName(), 'au');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u', 'au.authid=u.authid');
        $select->from->addInnerJoin(self::getName(), 'a', 'a.accountid=u.accountid');
        $select->from->addInnerJoin(Gpf_Db_Table_Roles::getName(), 'r', 'u.roleid=r.roleid');

        $authInfo->addWhere($select);
        $select->where->add('a.rstatus', 'IN', array(Gpf_Db_Account::APPROVED, Gpf_Db_Account::SUSPENDED));

        return $select->getAllRows();
    }
}

?>
