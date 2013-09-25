<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 33926 2011-07-26 13:34:04Z mkendera $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_User_AffiliatesGrid extends Pap_Common_StatsGrid {

    public function __construct() {
        parent::__construct(Pap_Stats_Table::USERID, 'u');
    }

    protected function createResultSelect() {
        parent::createResultSelect();
        $this->modifyResultSelect();
    }

    protected function modifyResultSelect() {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliatesGrid.createResultSelect',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('filters'), array($this->filters))));
    }

    protected function initViewColumns() {
        $this->addViewColumn('id', $this->_("ID"), true);
        $this->addViewColumn('refid', $this->_("Referral ID"), true);
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn('username', $this->_("Username"), true);
        $this->addViewColumn('ip', $this->_("IP address"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn('rstatus', $this->_("Status"), true);
        $this->addViewColumn('parent', $this->_("Parent"), true);
        $this->addViewColumn('dateinserted', $this->_("Date joined"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn('dateapproved', $this->_("Date approved"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn('originalparentuserid', $this->_("Orig. parent Id"), true);

        $this->addDynamicFields();

        $this->addViewColumn('minimumpayout', $this->_("Min. payout"));
        $this->addViewColumn('payoutmethod', $this->_("Payout method"));

        $this->addViewColumn('salesCount', $this->_("Sales"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('salesTotal', $this->_("Total cost of Sales"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('commissions', $this->_("Commissions of Sales"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('clicksRaw', $this->_("Raw clicks"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('clicksUnique', $this->_("Unique clicks"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('impressionsRaw', $this->_("Raw impressions"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('impressionsUnique', $this->_("Unique impressions"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('ctrRaw', $this->_("Clickthrough ratio"), true, Gpf_View_ViewColumn::TYPE_PERCENTAGE);
        $this->addViewColumn('scrRaw', $this->_("Conversion ratio"), true, Gpf_View_ViewColumn::TYPE_PERCENTAGE);
        $this->addViewColumn('avgCommissionPerClick', $this->_("Avg. com. per click"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('avgCommissionPerImp', $this->_("Avg. com. per impression"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('avgAmountOfOrder', $this->_("Avg. amount of order"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);

        $this->addViewColumn('lastlogin', $this->_("Last Login"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn('loginsnr', $this->_("Number of Logins"), true, Gpf_View_ViewColumn::TYPE_NUMBER);



        $this->addActionViewColumn();
    }

    protected function addActionViewColumn() {
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initRequiredColumns() {
        $this->addRequiredColumn('id');
        $this->addRequiredColumn('userid');
        $this->addRequiredColumn('firstname');
        $this->addRequiredColumn('lastname');
        $this->addRequiredColumn('parentuserid');
        $this->addRequiredColumn('parentfirstname');
        $this->addRequiredColumn('parentlastname');
    }

    protected function addDynamicFields() {
        $formFields = Gpf_Db_Table_FormFields::getInstance();
        $fields = $formFields->getFieldsNoRpc("affiliateForm", array(Gpf_Db_FormField::STATUS_MANDATORY,
        Gpf_Db_FormField::STATUS_OPTIONAL,
        Gpf_Db_FormField::STATUS_HIDDEN));
        foreach ($fields as $field) {
            $this->addViewColumn($field->get('code'), $this->_localize($field->get('name')), true);
        }
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('u.userid');
        $this->addDataColumn('userid', 'u.userid');
        $this->addDataColumn('refid', 'u.refid');
        $this->addDataColumn('firstname', 'au.firstname');
        $this->addDataColumn('lastname', 'au.lastname');
        $this->addDataColumn('username', 'au.username');
        $this->addDataColumn('notificationemail', 'au.notificationemail');
        $this->addDataColumn('ip', 'au.ip');
        $this->addDataColumn('originalparentuserid', 'u.originalparentuserid');
        $this->addDataColumn('rstatus', 'gu.rstatus');
        $this->addDataColumn('dateinserted', 'u.dateinserted');
        $this->addDataColumn('dateapproved', 'u.dateapproved');
        $this->addDataColumn('parentuserid', 'pu.userid');
        $this->addDataColumn('parentusername', 'pau.username');
        $this->addDataColumn('parentfirstname', 'pau.firstname');
        $this->addDataColumn('parentlastname', 'pau.lastname');
        for ($i = 1; $i <= 25; $i++) {
            $this->addDataColumn('data'.$i, 'u.data'.$i);
        }
        $this->addDataColumn('minimumpayout', 'u.minimumpayout');
        $this->addDataColumn('payoutmethod', 'pay.name');
        $this->addDataColumn('lastlogin', 'lo.lastlogin');
        $this->addDataColumn('loginsnr', 'lo.loginsnr');

        $this->initStatColumns();
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('refid', 60, 'N');
        $this->addDefaultViewColumn('name', 120, 'N');
        $this->addDefaultViewColumn('username', 120, 'N');
        $this->addDefaultViewColumn('rstatus', 40, 'N');
        $this->addDefaultViewColumn('parent', 120, 'N');
        $this->addDefaultViewColumn('dateinserted',60, 'D');
        $this->addDefaultViewColumn(self::ACTIONS, 60, 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Users::getName(), 'u');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(),
            'gu', 'u.accountuserid=gu.accountuserid AND u.' . Pap_Db_Table_Users::DELETED . ' = \'' . Gpf::NO . '\''.
        ' AND u.' .Pap_Db_Table_Users::TYPE . ' = \'' . Pap_Application::ROLETYPE_AFFILIATE . '\'');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),
            'au', 'au.authid=gu.authid');

        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Users::getName(),
            'pu', 'u.parentuserid=pu.userid');
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_Users::getName(),
            'pgu', 'pu.accountuserid=pgu.accountuserid');
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_AuthUsers::getName(),
            'pau', 'pau.authid=pgu.authid');

        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_FieldGroups::getName(),
            'pay', 'pay.fieldgroupid=u.payoutoptionid AND pay.rtype=\'P\' AND pay.rstatus=\'' .
        Gpf_Db_FieldGroup::ENABLED . '\'');


        if ($this->areColumnsRequiredOrInFilter(array('lastlogin', 'loginscount', 'loginsnr'))) {
            $select = new Gpf_SqlBuilder_SelectBuilder();
            $select->select->add('MAX(login)', 'lastlogin');
            $select->select->add('accountuserid');
            $select->select->add('COUNT(*)', 'loginsnr');
            $select->from->add(Gpf_Db_Table_LoginsHistory::getName());
            $select->groupBy->add(Gpf_Db_Table_LoginsHistory::ACCOUNTUSERID);

            $this->_selectBuilder->from->addLeftJoin('('.$select->toString().')',
                    'lo', 'u.' . Gpf_Db_Table_LoginsHistory::ACCOUNTUSERID . '=lo.'.Gpf_Db_Table_LoginsHistory::ACCOUNTUSERID);
        }
        $this->buildStatsFrom();
    }

    protected function buildOrder() {
        if ($this->_sortColumn == "name") {
            $this->_selectBuilder->orderBy->add("firstname", $this->_sortAsc, 'au');
            $this->_selectBuilder->orderBy->add("lastname", $this->_sortAsc, 'au');
            return;
        }
        parent::buildOrder();
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        if ($filter->getCode() == "search") {
            $this->addSearch($filter);
        }
    }

    /**
     * @param $select
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $preffixes = $select->where->getUniqueTablePreffixes();
        $preffixes['u'] = 'u';
        $preffixes['gu'] = 'gu';
    
        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from = clone $select->from;
        $count->from->prune($preffixes);
        $count->where = $select->where;
        $count->groupBy = $select->groupBy;
        $count->having = $select->having;
        return $count;
    }

    protected function loadResultData() {
        if (!$this->isStatsColumnRequired()) {
            $this->doMossoHack(Pap_Db_Table_Users::getInstance(), 'u', Pap_Db_Table_Users::ID);
        }
        return parent::loadResultData();
    }

    private function isStatsColumnRequired() {
        foreach (array_keys($this->statColumns) as $statsColumnName) {
            if ($this->isColumnRequired($statsColumnName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $params = new Pap_Stats_Params();
        $params->setStatus(Pap_Common_Constants::STATUS_APPROVED);
        return $this->addParamsWithDateRangeFilter($params);
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('u.userid', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('u.refid', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.username', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.firstname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.lastname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        for ($i=1; $i<=25; $i++) {
            $condition->add('u.data'.$i, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        }
        $condition->add('pau.username', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('pau.firstname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('pau.lastname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('pay.name', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.ip', 'LIKE', '%'.$filter->getValue().'%', 'OR');

        $this->_selectBuilder->where->addCondition($condition);
    }

    /**
     * @service affiliate read
     * @param Gpf_Rpc_Params $params
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addStringField("userid", $this->_("Id"));
        $filterFields->addStringField("firstname", $this->_("Firstname"));
        $filterFields->addStringField("lastname", $this->_("Lastname"));
        $filterFields->addStringField("username", $this->_("Username"));
        $filterFields->addDateField("dateinserted", $this->_("Date joined"));
        $filterFields->addStringField("parentuserid", $this->_("Parent ID"));
        $filterFields->addStringField("parentusername", $this->_("Parent username"));
        $filterFields->addStringField("parentfirstname", $this->_("Parent first name"));
        $filterFields->addStringField("parentlastname", $this->_("Parent last name"));
        $filterFields->addStringField("minimumpayout", $this->_("Min. payout"));
        $filterFields->addStringField("payoutmethod", $this->_("Payout method"));
        $filterFields->addStringField("ip", $this->_("IP address"));
        $filterFields->addNumberField("loginsnr", $this->_("Number of logins"));
        $filterFields->addDateField("lastlogin", $this->_("Last Login"));

        $formFields = Gpf_Db_Table_FormFields::getInstance();
        $fields = $formFields->getFieldsNoRpc("affiliateForm", array(Gpf_Db_FormField::STATUS_MANDATORY,
        Gpf_Db_FormField::STATUS_OPTIONAL,
        Gpf_Db_FormField::STATUS_HIDDEN));
        foreach ($fields as $field) {
            $type = $field->get('type');
            if ($type == Gpf_Db_FormField::TYPE_NUMBER) {
                $filterFields->addNumberField($field->get('code'), $this->_localize($field->get('name')));
            } else {
                $filterFields->addStringField($field->get('code'), $this->_localize($field->get('name')));
            }
        }

        $this->addStatCustomFilterFields($filterFields);

        return $filterFields->getRecordSet();
    }

    /**
     * @service affiliate read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service affiliate export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }

    /**
     * @service affiliate read
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        return parent::getRowCount($params);
    }
    
    protected function computeCount() {
        try{
            $this->_count = $this->createCountSelect($this->_selectBuilder)->getOneRow()->get('count');
        } catch(Gpf_DbEngine_NoRowException $e) {
            $this->_count = 0;
        }
    }
}
?>
