<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: CampaignsGrid.class.php 33162 2011-06-09 13:11:26Z iivanco $
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
class Pap_Merchants_Campaign_CampaignsGrid extends Gpf_View_MemoryGridService {
    /**
     * @var Pap_Db_Table_Commissions
     */
    private $commissionsTable;
    /**
     * @var Gpf_Data_RecordSet
     */
    private $commissions;

    public function __construct() {
        parent::__construct();
        $this->commissionsTable = Pap_Db_Table_Commissions::getInstance();
        $this->commissions = $this->commissionsTable->getAllCommissionsInCampaign();
    }

    protected function initViewColumns() {
        $this->addViewColumn('id', $this->_("ID"), true);
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn('commissionsdetails', $this->_('Commissions'));
        $this->addViewColumn('rstatus', $this->_("Status"), true);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CampaignsGrid.initViewColumns', $this);
        $this->addViewColumn('dateinserted', $this->_("Date created"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn('isdefault', $this->_("Default"), true);
        $this->addViewColumn('rorder', $this->_("Order"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('c.'.Pap_Db_Table_Campaigns::ID);
        $this->addDataColumn(Pap_Db_Table_Campaigns::ID, 'c.'.Pap_Db_Table_Campaigns::ID);
        $this->addDataColumn('rtype', 'c.rtype');
        $this->addDataColumn('rstatus','c.rstatus');
        $this->addDataColumn('name', 'c.name');
        $this->addDataColumn('description', 'c.description');
        $this->addDataColumn('dateinserted', 'c.dateinserted');
        $this->addDataColumn('rorder', 'c.rorder');
        $this->addDataColumn('isdefault', 'c.isdefault');
        $this->addDataColumn('networkstatus', 'c.networkstatus');
        $this->addDataColumn('logourl', 'c.logourl');
        $this->addDataColumn('productid', 'c.productid');
        $this->addDataColumn('discontinueurl', 'c.discontinueurl');
        $this->addDataColumn('validfrom', 'c.validfrom');
        $this->addDataColumn('validto', 'c.validto');
        $this->addDataColumn('validnumber', 'c.validnumber');
        $this->addDataColumn('validtype', 'c.validtype');
        $this->addDataColumn('accountid', 'c.accountid');
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CampaignsGrid.initDataColumns', $this);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('name', '150');
        $this->addDefaultViewColumn('commissionsdetails', '200');
        $this->addDefaultViewColumn('rstatus', '100');
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CampaignsGrid.initDefaultView', $this);
        $this->addDefaultViewColumn('isdefault', '50');
        $this->addDefaultViewColumn('dateinserted','100');
        $this->addDefaultViewColumn('rorder', '50', 'A');
        $this->addDefaultViewColumn(self::ACTIONS, '40');
    }

    public function filterRow(Gpf_Data_Row $row) {
        $row->add('commissionsexist', Gpf::NO);
        if ($this->commissionsTable->findCampaignInCommExistsRecords($row->get("id"), $this->commissions)) {
            $row->set('commissionsexist', Gpf::YES);
        }

        $row->add('commissionsdetails', $this->commissionsTable->getCommissionsDescription($row->get("id"),
        $this->commissions, $this->getCommissionGroupId($row)));

        if ($this->isAddCampaign($row)) {
            return parent::filterRow($row);
        }
        return null;
    }

    /**
     * @param $campaign
     * @return boolean
     */
    protected function isAddCampaign(Gpf_Data_Record $campaign) {
        return true;
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    protected function initResult() {
        $result = parent::initResult();
        $result->addColumn('commissionsexist', Gpf::NO);
        $result->addColumn('commissionsdetails', Gpf::NO);
        $result->addColumn('isdefault', Gpf::NO);

        return $result;
    }

    protected function getCommissionGroupId(Gpf_Data_Record $campaign) {
        return null;
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Campaigns::getName(), 'c');
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyFrom',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder,
        new Gpf_Data_Record(array('joinedAlias', 'onJoinAlias'), array('a', 'c'))));
    }

    protected function buildWhere() {
        parent::buildWhere();
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix'), array('c'))), $this->filters);
        Gpf_Plugins_Engine::extensionPoint('CampaignGrid.modifyWhere',
        new Pap_Affiliates_Promo_SelectBuilderCompoundFilter($this->_selectBuilder, $this->filters));
    }

    protected function buildFilter() {
        if ($this->filters->getSize() == 0 ) {
            $this->_selectBuilder->where->add('c.rstatus', '<>', Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED_INVISIBLE);
        } else {
            parent::buildFilter();
        }
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
        }
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('c.'.Pap_Db_Table_Campaigns::NAME , 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('c.'.Pap_Db_Table_Campaigns::ID , 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $this->_selectBuilder->where->addCondition($condition);
    }

    /**
     * @service campaign read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service campaign read
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        return parent::getRowCount($params);
    }

    /**
     * @service campaign export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
