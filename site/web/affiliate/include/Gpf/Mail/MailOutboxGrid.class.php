<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailOutboxGrid.class.php 29054 2010-08-12 10:57:31Z mkendera $
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
class Gpf_Mail_MailOutboxGrid extends Gpf_View_GridService {

	protected function initViewColumns() {
		$this->addViewColumn(Gpf_Db_Table_Mails::TO_RECIPIENTS, $this->_("To recipients"), true);
		$this->addViewColumn(Gpf_Db_Table_Mails::SUBJECT, $this->_("Mail subject"), true);
		$this->addViewColumn(Gpf_Db_Table_MailOutbox::STATUS, $this->_("Status"), true);
		$this->addViewColumn(Gpf_Db_Table_MailOutbox::SCHNEDULET_AT, $this->_("Scheduled at"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
		$this->addViewColumn(Gpf_Db_Table_MailOutbox::LASTR_RETRY, $this->_("Last retry"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
		$this->addViewColumn(Gpf_Db_Table_MailOutbox::RETRY_NR, $this->_("Retry number"), true);
		$this->addViewColumn(Gpf_Db_Table_MailOutbox::ERROR_MSG, $this->_("Error message"), true);
		$this->addViewColumn(Gpf_Db_Table_MailAccounts::ACCOUNT_NAME, $this->_("Account name"), true);
		$this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
	}

	protected function initDataColumns() {
		$this->setKeyDataColumn(Gpf_Db_Table_MailOutbox::ID);
		$this->addDataColumn(Gpf_Db_Table_Mails::TO_RECIPIENTS, Gpf_Db_Table_Mails::TO_RECIPIENTS);
		$this->addDataColumn(Gpf_Db_Table_Mails::SUBJECT, Gpf_Db_Table_Mails::SUBJECT);
		$this->addDataColumn(Gpf_Db_Table_MailOutbox::STATUS, Gpf_Db_Table_MailOutbox::STATUS);
		$this->addDataColumn(Gpf_Db_Table_MailOutbox::SCHNEDULET_AT, Gpf_Db_Table_MailOutbox::SCHNEDULET_AT);
		$this->addDataColumn(Gpf_Db_Table_MailOutbox::LASTR_RETRY, Gpf_Db_Table_MailOutbox::LASTR_RETRY);
		$this->addDataColumn(Gpf_Db_Table_MailOutbox::RETRY_NR, Gpf_Db_Table_MailOutbox::RETRY_NR);
		$this->addDataColumn(Gpf_Db_Table_MailOutbox::ERROR_MSG, Gpf_Db_Table_MailOutbox::ERROR_MSG);
		$this->addDataColumn(Gpf_Db_Table_MailAccounts::ACCOUNT_NAME, Gpf_Db_Table_MailAccounts::ACCOUNT_NAME);
	}

	protected function initDefaultView() {
		$this->addDefaultViewColumn(Gpf_Db_Table_Mails::TO_RECIPIENTS, '40px', 'N');
		$this->addDefaultViewColumn(Gpf_Db_Table_Mails::SUBJECT, '40px', 'N');
		$this->addDefaultViewColumn(Gpf_Db_Table_MailOutbox::STATUS, '40px', 'N');
		$this->addDefaultViewColumn(Gpf_Db_Table_MailOutbox::SCHNEDULET_AT, '40px', 'D');
		$this->addDefaultViewColumn(Gpf_Db_Table_MailOutbox::LASTR_RETRY, '40px', 'N');
		$this->addDefaultViewColumn(Gpf_Db_Table_MailOutbox::RETRY_NR, '40px', 'N');
		$this->addDefaultViewColumn(Gpf_Db_Table_MailOutbox::ERROR_MSG, '40px', 'N');
		$this->addDefaultViewColumn(Gpf_Db_Table_MailAccounts::ACCOUNT_NAME, '40px', 'N');
		$this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
	}



	protected function buildFrom() {
		$this->_selectBuilder->from->add(Gpf_Db_Table_MailOutbox::getName());

		$condition = Gpf_Db_Table_MailOutbox::getName().".".Gpf_Db_Table_MailOutbox::MAILACCOUNTID." = ".Gpf_Db_Table_MailAccounts::getName().".".Gpf_Db_Table_MailAccounts::ID;
		$this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_MailAccounts::getName(), '', $condition);

		$condition = Gpf_Db_Table_MailOutbox::getName().".".Gpf_Db_Table_MailOutbox::MAIL_ID." = ".Gpf_Db_Table_Mails::getName().".".Gpf_Db_Table_Mails::ID;
		$this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Mails::getName(), '', $condition);
	}

	//custom handling of to_recipients filter
    protected function buildFilter() {
        foreach ($this->filters as $filter) {
            if ($filter->getCode() != 'to_recipients' && array_key_exists($filter->getCode(), $this->dataColumns)) {
                $dataColumn = $this->dataColumns[$filter->getCode()];
                $filter->setCode($dataColumn->getName());
                $filter->addTo($this->_selectBuilder->where);
            } else {
                $this->addFilter($filter);
            }
        }
    }


	protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
		switch ($filter->getCode()) {
			case "search":
				$this->addSearchFilter($filter);
				break;
            case "to_recipients":
                $this->addRecipientsFilter($filter);
                break;
		}
	}

    private function addRecipientsFilter(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add(Gpf_Db_Table_Mails::TO_RECIPIENTS, '=', $filter->getValue(),'OR');
        try {
            $dbUser = new Gpf_Db_AuthUser();
            $dbUser->setUsername($filter->getValue());
            $dbUser->loadFromData(array(Gpf_Db_Table_AuthUsers::USERNAME));
            if (strlen($dbUser->getNotificationEmail())) {
                $condition->add(Gpf_Db_Table_Mails::TO_RECIPIENTS, '=', $dbUser->getNotificationEmail(),'OR');
            }
        } catch (Exception $e) {
        }
        $this->_selectBuilder->where->addCondition($condition);
    }

	private function addSearchFilter(Gpf_SqlBuilder_Filter $filter) {
		$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
		$condition->add(Gpf_Db_Table_Mails::SUBJECT, 'LIKE', '%'.$filter->getValue().'%');
		$condition->add(Gpf_Db_Table_Mails::BODY_TEXT, 'LIKE', '%'.$filter->getValue().'%','OR');
		$condition->add(Gpf_Db_Table_Mails::BODY_HTML, 'LIKE', '%'.$filter->getValue().'%','OR');
		$condition->add(Gpf_Db_Table_Mails::TO_RECIPIENTS, 'LIKE', '%'.$filter->getValue().'%','OR');
		$condition->add(Gpf_Db_Table_Mails::CC_RECIPIENTS, 'LIKE', '%'.$filter->getValue().'%','OR');
		$condition->add(Gpf_Db_Table_Mails::BCC_RECIPIENTS, 'LIKE', '%'.$filter->getValue().'%','OR');
		$condition->add(Gpf_Db_Table_Mails::FROM_MAIL, 'LIKE', '%'.$filter->getValue().'%','OR');
		$this->_selectBuilder->where->addCondition($condition);
	}

	/**
	 * @service mail_outbox read
	 * @return Gpf_Rpc_Serializable
	 */
	public function getRows(Gpf_Rpc_Params $params) {
		return parent::getRows($params);
	}

	/**
	 * @service mail_outbox export
	 * @return Gpf_Rpc_Serializable
	 */
	public function getCSVFile(Gpf_Rpc_Params $params) {
		return parent::getCSVFile($params);
	}

	protected function createHeader($views) {
	    $header = parent::createHeader($views);
	    $key = array_search("attachments", $header);
	    if ($key === false) {
	        return $header;
	    }
	    unset($header[$key]);
	    $header = array_values($header);
	    return $header;
	}
}
?>
