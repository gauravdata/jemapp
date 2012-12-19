<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Logs.class.php 27761 2010-04-14 07:45:41Z vzeman $
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
class Pap_Merchants_Tools_VisitorAffiliatesGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::ID, $this->_("ID"), true);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::VISITORID, $this->_("Visitor ID"), true);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::USERID, $this->_("Affiliate"), true);
        $this->addViewColumn('banner', $this->_('Banner'), true);
        $this->addViewColumn(Pap_Db_Table_Campaigns::NAME, $this->_("Campaign Name"), true);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::CHANNELID, $this->_("Channel"), true);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::TYPE, $this->_("Type"), true);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::IP, $this->_("IP"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::DATEVISIT, $this->_("Date of visit"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::VALIDTO, $this->_("Valid to"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::REFERRERURL, $this->_("Referrer URL"), true);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::DATA1, $this->_("Data 1"), true);
        $this->addViewColumn(Pap_Db_Table_VisitorAffiliates::DATA2, $this->_("Data 2"), true);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.VisitorAffiliatesGrid.initViewColumns', $this);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_VisitorAffiliates::ID);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::ID, "va.".Pap_Db_Table_VisitorAffiliates::ID);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::VISITORID, "va.".Pap_Db_Table_VisitorAffiliates::VISITORID);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::USERID, "va.".Pap_Db_Table_VisitorAffiliates::USERID);
        $this->addDataColumn('username',                                        "au.username");
        $this->addDataColumn('firstname',                                        "au.firstname");
        $this->addDataColumn('lastname',                                        "au.lastname");
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::BANNERID, "va.".Pap_Db_Table_VisitorAffiliates::BANNERID);
        $this->addDataColumn('banner_name', "b.".Pap_Db_Table_Banners::NAME);
        $this->addDataColumn('banner_type', "b.".Pap_Db_Table_Banners::TYPE);
        $this->addDataColumn('banner_id', "b.".Pap_Db_Table_Banners::ID);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::CHANNELID, "va.".Pap_Db_Table_VisitorAffiliates::CHANNELID);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::CAMPAIGNID, "va.".Pap_Db_Table_VisitorAffiliates::CAMPAIGNID);
        $this->addDataColumn(Pap_Db_Table_Campaigns::NAME,                "c.".Pap_Db_Table_Campaigns::NAME);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::CHANNELID, 'ch.'.Pap_Db_Table_Channels::NAME);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::TYPE, "va.".Pap_Db_Table_VisitorAffiliates::TYPE);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::IP, "va.".Pap_Db_Table_VisitorAffiliates::IP);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::DATEVISIT, "va.".Pap_Db_Table_VisitorAffiliates::DATEVISIT);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::VALIDTO, "va.".Pap_Db_Table_VisitorAffiliates::VALIDTO);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::REFERRERURL, "va.".Pap_Db_Table_VisitorAffiliates::REFERRERURL);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::DATA1, "va.".Pap_Db_Table_VisitorAffiliates::DATA1);
        $this->addDataColumn(Pap_Db_Table_VisitorAffiliates::DATA2, "va.".Pap_Db_Table_VisitorAffiliates::DATA2);
        $this->addDataColumn('accountid', "va.".Pap_Db_Table_VisitorAffiliates::ACCOUNTID);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.VisitorAffiliatesGrid.initDataColumns', $this);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_VisitorAffiliates::ID, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_VisitorAffiliates::VISITORID, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_VisitorAffiliates::USERID, '', 'N');
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.VisitorAffiliatesGrid.initDefaultView', $this);
        $this->addDefaultViewColumn('banner', '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Campaigns::NAME, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_VisitorAffiliates::TYPE, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_VisitorAffiliates::IP, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_VisitorAffiliates::DATEVISIT, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_VisitorAffiliates::VALIDTO, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_VisitorAffiliates::REFERRERURL, '', 'N');
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
        }
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_VisitorAffiliates::getName(), "va");
        $onCondition = "va.".Pap_Db_Table_VisitorAffiliates::CAMPAIGNID." = c.".Pap_Db_Table_Campaigns::ID;
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Campaigns::getName(), 'c', $onCondition);
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "va.userid = pu.userid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Channels::getName(), "ch", "va.channelid = ch.channelid");
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Banners::getName(), 'b', 'b.bannerid = va.bannerid');
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyFrom',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder,
        new Gpf_Data_Record(array('joinedAlias', 'onJoinAlias'), array('a', 'va'))));
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $this->_selectBuilder->where->add("va.".Pap_Db_Table_VisitorAffiliates::IP, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $this->_selectBuilder->where->add("va.".Pap_Db_Table_VisitorAffiliates::VISITORID, 'LIKE', '%' . $filter->getValue() .'%', "OR");
    }

    protected function buildWhere() {
        parent::buildWhere();
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix'), array('va'))));
    }

    /**
     * @service visitor_affiliates read
     * @param Gpf_Rpc_Params $params
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addStringField(Pap_Db_Table_VisitorAffiliates::IP, $this->_("IP"));
        $filterFields->addStringField(Pap_Db_Table_VisitorAffiliates::VISITORID, $this->_("Visitor ID"));

        return $filterFields->getRecordSet();
    }

    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $preffixes = $select->where->getUniqueTablePreffixes();
        $preffixes['va'] = 'va';

        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from = clone $select->from;
        $count->from->prune($preffixes);
        $count->where = $select->where;
        $count->groupBy = $select->groupBy;
        $count->having = $select->having;
        return $count;
    }

    /**
     * @service visitor_affiliates read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
}
?>
