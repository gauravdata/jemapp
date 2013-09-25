<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.6
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
class Gpf_Newsletter_BroadcastsGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_Broadcasts::CREATED, $this->_("Created"), true);
        $this->addViewColumn(Gpf_Db_Table_Broadcasts::MODIFIED, $this->_("Modified"), true);
        $this->addViewColumn(Gpf_Db_Table_Broadcasts::SCHEDULED, $this->_("Scheduled"), true);
        $this->addViewColumn(Gpf_Db_Table_Broadcasts::STATUS, $this->_("Status"), true);
        $this->addViewColumn(Gpf_Db_Table_MailTemplates::SUBJECT, $this->_("Subject"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Broadcasts::ID);
        $this->addDataColumn(Gpf_Db_Table_Broadcasts::ID, 'b.'.Gpf_Db_Table_Broadcasts::ID);
        $this->addDataColumn(Gpf_Db_Table_Broadcasts::CREATED, 'b.'.Gpf_Db_Table_Broadcasts::CREATED);
        $this->addDataColumn(Gpf_Db_Table_Broadcasts::MODIFIED, 'b.'.Gpf_Db_Table_Broadcasts::MODIFIED);
        $this->addDataColumn(Gpf_Db_Table_Broadcasts::SCHEDULED, 'b.'.Gpf_Db_Table_Broadcasts::SCHEDULED);
        $this->addDataColumn(Gpf_Db_Table_Broadcasts::STATUS, 'b.'.Gpf_Db_Table_Broadcasts::STATUS);
        $this->addDataColumn(Gpf_Db_Table_MailTemplates::SUBJECT, Gpf_Db_Table_MailTemplates::SUBJECT);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_Broadcasts::CREATED, '40px', 'A');
        $this->addDefaultViewColumn(Gpf_Db_Table_MailTemplates::SUBJECT, '200px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Broadcasts::STATUS, '40px', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_Broadcasts::getName(), 'b');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_MailTemplates::getName(), 't', 'b.templateid=t.templateid');
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add(Gpf_Db_Table_Newsletters::ID, '=', $this->getParam(Gpf_Db_Table_Newsletters::ID));
    }

    /**
     * @service newsletter read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service newsletter export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
