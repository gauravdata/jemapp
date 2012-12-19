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
class Pap_Affiliates_Reports_ChannelStatsGrid extends Pap_Common_StatsGrid {

    /**
     * @var Gpf_SqlBuilder_SelectBuilder
     */
    protected $channelsSelect;

    public function __construct() {
        parent::__construct(Pap_Stats_Table::CHANNEL, 'ch', Pap_Db_Table_Channels::ID);
    }

    protected function initViewColumns() {
        $this->addViewColumn('channel', $this->_("Channel"), true);
        $this->addViewColumn("impressionsRaw", $this->_("Impressions raw"), false);
        $this->addViewColumn("impressionsUnique", $this->_("Impressions unique"), false);

        $this->addViewColumn("clicksRaw", $this->_("Clicks raw"), false);
        $this->addViewColumn("ctrRaw", $this->_("CTR raw"), false);

        $this->addViewColumn("clicksUnique", $this->_("Clicks unique"), false);
        $this->addViewColumn("ctrUnique", $this->_("CTR unique"), false);

        $this->addViewColumn("salesCount", $this->_("Sales count"), false);
        $this->addViewColumn("salesTotal", $this->_("Sales total"), false);
        $this->addViewColumn("commissions", $this->_("Commissions"), false);
        $this->addAllActionsViewColumns();
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_Channels::ID);
        $this->addDataColumn('channel', 'ch.'.Pap_Db_Table_Channels::NAME);
        $this->initStatColumns();
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn("channel", '', 'A');
        $this->addDefaultViewColumn("salesCount", '', 'N');
        $this->addDefaultViewColumn("impressionsRaw", '', 'N');
        $this->addDefaultViewColumn("clicksRaw", '', 'N');
        $this->addDefaultViewColumn("commissions", '', 'N');
    }

    protected function createResultSelect() {
        $this->initChannelsSelect();
        parent::createResultSelect();
    }

    function buildFrom() {
        $this->_selectBuilder->from->add('(' . $this->createChannelsWithEmptyRow($this->channelsSelect) .')', 'ch');
        $this->buildStatsFrom();
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        if ($filter->getCode() == "search") {
            $this->addSearch($filter);
        }
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $params = parent::getStatsParameters();
        return $this->addParamsWithDateRangeFilter($params);
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add(Pap_Db_Table_Channels::NAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');

        $this->_selectBuilder->where->addCondition($condition);
    }

    /**
     * @service sub_aff_sale read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    protected function initChannelsSelect() {
        $this->channelsSelect = new Gpf_SqlBuilder_SelectBuilder();
        $this->channelsSelect->select->add(Pap_Db_Table_Channels::ID);
        $this->channelsSelect->select->add(Pap_Db_Table_Channels::NAME);
        $this->channelsSelect->from->add(Pap_Db_Table_Channels::getName());
        $this->channelsSelect->where->add(Pap_Db_Table_Channels::USER_ID, '=', Gpf_Session::getAuthUser()->getPapUserId());
    }

    /**
     * @return String
     */
    protected function createChannelsWithEmptyRow(Gpf_SqlBuilder_SelectBuilder $select) {
        $selectNullRow = new Gpf_SqlBuilder_SelectClause();
        $selectNullRow->add('\'\'', Pap_Db_Table_Channels::ID);
        $selectNullRow->add('\' ' . $this->_('None channel') . '\'', Pap_Db_Table_Channels::NAME);

        return $select->toString() . ' UNION ' . $selectNullRow->toString();
    }
}
?>
