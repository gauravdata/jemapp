<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Logs.class.php 19394 2008-07-25 09:11:14Z mfric $
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
class Gpf_Report_OnlineUsers extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, $this->_("Username"), true);
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, $this->_("First Name"), true);
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::LASTNAME, $this->_("Last Name"), true);
        $this->addViewColumn(Gpf_Db_Table_Roles::NAME, $this->_("Role"), true);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::LOGIN, $this->_("Logged in"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, $this->_("Last Request"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::IP, $this->_("IP"), true, Gpf_View_ViewColumn::TYPE_IP);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_LoginsHistory::ID);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::USERNAME, Gpf_Db_Table_AuthUsers::USERNAME);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::LASTNAME, Gpf_Db_Table_AuthUsers::LASTNAME);
        $this->addDataColumn(Gpf_Db_Table_Roles::NAME, Gpf_Db_Table_Roles::NAME);
        $this->addDataColumn(Gpf_Db_Table_Roles::ID, 'r.' . Gpf_Db_Table_Roles::ID);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::LOGIN, Gpf_Db_Table_LoginsHistory::LOGIN);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, Gpf_Db_Table_LoginsHistory::LAST_REQUEST);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::IP, 'l.'.Gpf_Db_Table_LoginsHistory::IP);
        $this->addDataColumn(Gpf_Db_Table_Users::ACCOUNTID, 'u.'.Gpf_Db_Table_Users::ACCOUNTID);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::LASTNAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Roles::NAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::LOGIN, '40px', 'D');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::IP, '40px', 'N');
    }

    protected function buildWhere() {
        parent::buildWhere();        
        $this->_selectBuilder->where->add('l.logout', 'is', 'NULL', 'AND', false);
        $this->_selectBuilder->where->add(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, '>',
        "('" . $this->createDatabase()->getDateString() . "' - INTERVAL 1800 SECOND)", 'AND', false);
        Gpf_Plugins_Engine::extensionPoint('Gpf_Report_OnlineUsers.buildWhere', $this->_selectBuilder->where);
    }


    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_LoginsHistory::getName(), 'l');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u', 'l.accountuserid=u.accountuserid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'u.authid=au.authid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Roles::getName(), 'r', 'r.roleid=u.roleid');
    }

    /**
     * @service online_user read
     * @param Gpf_Rpc_Params $params
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addStringField(Gpf_Db_Table_LoginsHistory::IP, $this->_("IP"));
        $filterFields->addStringField(Gpf_Db_Table_AuthUsers::USERNAME, $this->_("Username"));
        $filterFields->addStringField(Gpf_Db_Table_AuthUsers::FIRSTNAME, $this->_("First Name"));
        $filterFields->addStringField(Gpf_Db_Table_AuthUsers::LASTNAME, $this->_("Last Name"));
        return $filterFields->getRecordSet();
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;

        }
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $cond = new Gpf_SqlBuilder_CompoundWhereCondition();
        $cond->add(Gpf_Db_Table_AuthUsers::USERNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $cond->add(Gpf_Db_Table_AuthUsers::FIRSTNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $cond->add(Gpf_Db_Table_AuthUsers::LASTNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $cond->add(Gpf_Db_Table_Roles::NAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $cond->add('l.'.Gpf_Db_Table_LoginsHistory::IP, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $this->_selectBuilder->where->addCondition($cond);
    }
    
    /**
     * @service online_user read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service online_user export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
