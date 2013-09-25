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
class Gpf_Report_LoginsHistory extends Gpf_View_GridService {
    const DATE_ONE_WEEK = 'OW';
    const DATE_TWO_WEEKS = 'TW';
    const DATE_ONE_MONTH = 'OM';

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, $this->_("Username"), true);
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, $this->_("First Name"), true);
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::LASTNAME, $this->_("Last Name"), true);
        $this->addViewColumn(Gpf_Db_Table_Roles::NAME, $this->_("Role"), true);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::LOGIN, $this->_("Logged in"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, $this->_("Last Request"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::LOGOUT, $this->_("Logged out"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::IP, $this->_("IP"), true, Gpf_View_ViewColumn::TYPE_IP);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('l.'.Gpf_Db_Table_LoginsHistory::ID);
        $this->addDataColumn(Gpf_Db_Table_Users::ID, 'u.'.Gpf_Db_Table_Users::ID);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::USERNAME, 'au.'.Gpf_Db_Table_AuthUsers::USERNAME);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, 'au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::LASTNAME, 'au.'.Gpf_Db_Table_AuthUsers::LASTNAME);
        $this->addDataColumn(Gpf_Db_Table_Roles::NAME, 'r.'.Gpf_Db_Table_Roles::NAME);
        $this->addDataColumn(Gpf_Db_Table_Roles::ID, 'r.' . Gpf_Db_Table_Roles::ID);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::LOGIN, 'l.'.Gpf_Db_Table_LoginsHistory::LOGIN);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::LOGOUT, 'l.'.Gpf_Db_Table_LoginsHistory::LOGOUT);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, 'l.'.Gpf_Db_Table_LoginsHistory::LAST_REQUEST);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::IP, 'l.'.Gpf_Db_Table_LoginsHistory::IP);
        $this->addDataColumn(Gpf_Db_Table_Users::ACCOUNTID, 'u.'.Gpf_Db_Table_Users::ACCOUNTID);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::LASTNAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Roles::NAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::LOGIN, '40px', 'D');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::LOGOUT, '40px', 'D');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::IP, '40px', 'N');
    }

    protected function buildWhere() {
        parent::buildWhere();
        Gpf_Plugins_Engine::extensionPoint('Gpf_Report_LoginsHistory.buildWhere', $this->_selectBuilder->where);
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

    /**
     * @param $select
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $countSelectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $countSelectBuilder->select->add('count(l.loginid)', 'count');
        $countSelectBuilder->from = $select->from;
        $countSelectBuilder->where = $select->where;
        $countSelectBuilder->groupBy = $select->groupBy;
        $countSelectBuilder->having = $select->having;
        return $countSelectBuilder;
    }

   protected function loadResultData() {
        $this->doMossoHack(Gpf_Db_Table_LoginsHistory::getInstance(), 'l', Gpf_Db_Table_LoginsHistory::ID);
        return parent::loadResultData();
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $cond = new Gpf_SqlBuilder_CompoundWhereCondition();
        $cond->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $cond->add('au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $cond->add('au.'.Gpf_Db_Table_AuthUsers::LASTNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $cond->add('r.'.Gpf_Db_Table_Roles::NAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
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

    /**
     *
     * @service online_user delete
     * @param ids, status
     * @return Gpf_Rpc_Action
     */
    public function deleteLogins(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);

        $date = array();
        switch ($action->getParam("status")) {
            case self::DATE_ONE_WEEK:
                $filter = new Gpf_SqlBuilder_Filter(array("", "DP", "L7D"));
                $date = $filter->addDateValueToArray($date);
                $olderThan = $this->_("one week");
                break;
            case self::DATE_TWO_WEEKS:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                     Gpf_Common_DateUtils::getServerTime(
                         mktime(0,0,0,date("m"), date("d") - 14, date("Y"))));
                $date = array("dateFrom" => $dateFrom);
                $olderThan = $this->_("two weeks");
                break;
            case self::DATE_ONE_MONTH:
                $filter = new Gpf_SqlBuilder_Filter(array("", "DP", "L30D"));
                $date = $filter->addDateValueToArray($date);
                $olderThan = $this->_("one month");
                break;
        }

        $action->setInfoMessage($this->_("Login history older than %s was deleted", $olderThan));
        $action->setErrorMessage($this->_("Failed to delete login history"));

        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Gpf_Db_Table_LoginsHistory::getName());
        $delete->where->add(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, "<", $date["dateFrom"]);

        try {
            $delete->delete();
            $action->addOk();
        } catch(Gpf_DbEngine_NoRowException $e) {
            $action->addError();
        }

        return $action;
    }


}
?>
