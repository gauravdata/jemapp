<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailTemplates.class.php 28892 2010-07-26 09:02:20Z iivanco $
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
class Gpf_Db_Table_MailTemplates extends Gpf_DbEngine_Table {
    const ID = 'templateid';
    const ACCOUNT_ID = 'accountid';
    const BODY_HTML = 'body_html';
    const BODY_TEXT = 'body_text';
    const SUBJECT = 'subject';
    const TEMPLATE_NAME = 'templatename';
    const CLASS_NAME = 'classname';
    const IS_CUSTOM = 'is_custom';
    const CREATED = 'created';
    const USERID = 'userid';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_mail_templates');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::CLASS_NAME, 'varchar', 255);
        $this->createColumn(self::TEMPLATE_NAME, 'varchar', 255);
        $this->createColumn(self::SUBJECT, 'text');
        $this->createColumn(self::BODY_HTML, 'text');
        $this->createColumn(self::BODY_TEXT, 'text');
        $this->createColumn(self::ACCOUNT_ID, 'char', 8);
        $this->createColumn(self::IS_CUSTOM, 'char', 1, true);
        $this->createColumn(self::CREATED, 'datetime');
        $this->createColumn(self::USERID, 'char', 8);
    }

    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_SmartyConstraint(self::SUBJECT));
        $this->addConstraint(new Gpf_DbEngine_Row_SmartyConstraint(self::BODY_HTML));
        $this->addConstraint(new Gpf_DbEngine_Row_SmartyConstraint(self::BODY_TEXT));
    }

    public function deleteAll($templateId) {
        $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBulider->from->add(self::getName());
        $deleteBulider->where->add(self::ID, '=', $templateId);
        $this->createDatabase()->execute($deleteBulider->toString());

        $deleteAttachments = Gpf_Db_Table_MailTemplateAttachments::getInstance();
        $deleteAttachments->deleteAll($templateId);
    }


    /**
     * Return list of all system mail templates
     * @service mail_template read
     */
    public function getAllSystemTemplates() {
        $sql = new Gpf_SqlBuilder_SelectBuilder();
        $sql->select->add(Gpf_Db_Table_MailTemplates::ID);
        $sql->select->add(Gpf_Db_Table_MailTemplates::TEMPLATE_NAME);
        $sql->from->add(Gpf_Db_Table_MailTemplates::getName());
        $sql->where->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
        $sql->where->add(Gpf_Db_Table_MailTemplates::IS_CUSTOM, '=', Gpf::NO);
        return $sql->getAllRows();
    }
}
?>
