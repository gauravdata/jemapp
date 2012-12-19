<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Pap_Features_MultipleMerchants_AdminsGrid extends Gpf_View_GridService implements Gpf_View_Grid_HasRowFilter {

    private $accountId = null;

    public function isAccountColumnVisible() {
        return $this->accountId == null;
    }
    
    protected function initViewColumns() {
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn('username', $this->_("Username"), true);
        $this->addViewColumn('rstatus', $this->_("Status"), true);
        $this->addViewColumn('dateinserted', $this->_("Date joined"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn('isdefault', $this->_('Default'));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AdminsGrid.initViewColumns', $this);
        $this->addViewColumn(parent::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('u.userid');
        $this->addDataColumn('firstname', 'au.firstname');
        $this->addDataColumn('lastname', 'au.lastname');
        $this->addDataColumn('username', 'au.username');
        $this->addDataColumn('accountid', 'a.accountid');
        $this->addDataColumn('account', 'a.name');
        $this->addDataColumn('roleid', 'r.roleid');
        $this->addDataColumn('role', 'r.name');
        $this->addDataColumn('rstatus', 'gu.rstatus');
        $this->addDataColumn('dateinserted', 'u.dateinserted');
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('name', '', 'N');
        $this->addDefaultViewColumn('username', '', 'N');
        $this->addDefaultViewColumn('rstatus', '', 'N');
        $this->addDefaultViewColumn('dateinserted','', 'D');
        $this->addDefaultViewColumn('isdefault', '');
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AdminsGrid.initDefaultView', $this);
        $this->addDefaultViewColumn(parent::ACTIONS, '', 'N');
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add('u.deleted', '=', Gpf::NO);
        $this->_selectBuilder->where->add('u.rtype', '=', Pap_Application::ROLETYPE_MERCHANT);
        if ($this->accountId != null) {
            $this->_selectBuilder->where->add('a.accountid', '=', $this->accountId);
        }
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix'), array('a'))));
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Users::getName(), 'u');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(),
            'gu', 'u.accountuserid=gu.accountuserid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),
            'au', 'au.authid=gu.authid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Accounts::getName(),
            'a', 'a.accountid=gu.accountid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Roles::getName(),
            'r', 'r.roleid=gu.roleid');
    }
    
    /**
     * @param $select
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $countSelectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $countSelectBuilder->select->add('count(u.userid)', 'count');
        $countSelectBuilder->from = $select->from;
        $countSelectBuilder->where = $select->where;
        $countSelectBuilder->groupBy = $select->groupBy;
        $countSelectBuilder->having = $select->having;
        
        return $countSelectBuilder;
    }

    protected function buildOrder() {
        if ($this->_sortColumn == "name") {
            $this->_selectBuilder->orderBy->add("au.firstname", $this->_sortAsc);
            $this->_selectBuilder->orderBy->add("au.lastname", $this->_sortAsc);
            return;
        }
        parent::buildOrder();
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    protected function initResult() {
        $result = parent::initResult();
        $result->getHeader()->add('isdefault');
        return $result;
    }

    public function filterRow(Gpf_Data_Row $row) {
        $row->add('isdefault', Gpf::NO);
        if ($row->get('id') == Gpf_Settings::get(Pap_Settings::DEFAULT_MERCHANT_ID)) {
            $row->set('isdefault', Gpf::YES);
        }
        return $row;
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
        }
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $this->_selectBuilder->where->add("au.".Gpf_Db_Table_AuthUsers::FIRSTNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $this->_selectBuilder->where->add("au.".Gpf_Db_Table_AuthUsers::LASTNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $this->_selectBuilder->where->add("a.".Gpf_Db_Table_Accounts::ID, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $this->_selectBuilder->where->add("a.".Gpf_Db_Table_Accounts::NAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
    }

    /**
     * @service merchant read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        if ($params->get('accountid') != null) {
            $this->accountId = $params->get('accountid');
        }
        return parent::getRows($params);
    }
}
?>
