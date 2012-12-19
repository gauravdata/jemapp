<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailOutboxGrid.class.php 24330 2009-05-06 08:05:53Z jsimon $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Mail_SentMailTemplatesGrid extends Gpf_View_GridService {

	protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_MailTemplates::CREATED, $this->_("Created"), true);
	    $this->addViewColumn(Gpf_Db_Table_MailTemplates::SUBJECT, $this->_("Subject"), true);
		$this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
	}

	protected function initDataColumns() {
		$this->setKeyDataColumn(Gpf_Db_Table_MailTemplates::ID);
        $this->addDataColumn(Gpf_Db_Table_MailTemplates::CREATED, Gpf_Db_Table_MailTemplates::CREATED);
		$this->addDataColumn(Gpf_Db_Table_MailTemplates::SUBJECT, Gpf_Db_Table_MailTemplates::SUBJECT);
	}

	protected function initDefaultView() {
		$this->addDefaultViewColumn(Gpf_Db_Table_MailTemplates::CREATED, '40px', 'D');
		$this->addDefaultViewColumn(Gpf_Db_Table_MailTemplates::SUBJECT, '100px', 'N');
		$this->addDefaultViewColumn(self::ACTIONS, '20px', 'N');
	}

	protected function buildFrom() {
		$this->_selectBuilder->from->add(Gpf_Db_Table_MailTemplates::getName());
	}

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
        $this->_selectBuilder->where->add(Gpf_Db_Table_MailTemplates::IS_CUSTOM, '=', Gpf::YES);
        $this->_selectBuilder->where->add(Gpf_Db_Table_MailTemplates::USERID, '=', Gpf_Session::getAuthUser()->getUserId());
    }

    protected function initLimit() {
        parent::initLimit();
        $this->limit = 15;
    }

	/**
	 * @service mail_template read
	 * @return Gpf_Rpc_Serializable
	 */
	public function getRows(Gpf_Rpc_Params $params) {
		return parent::getRows($params);
	}

	/**
	 * @service mail_template export
	 * @return Gpf_Rpc_Serializable
	 */
	public function getCSVFile(Gpf_Rpc_Params $params) {
		return parent::getCSVFile($params);
	}
}
?>
