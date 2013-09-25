<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 23478 2009-02-12 14:48:17Z aharsani $
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
class Pap_Features_PapGeoip_MapOverlayGrid extends Pap_Common_StatsGrid {

    function __construct() {
        parent::__construct(Pap_Stats_Table::COUNTRYCODE, 'c');
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('c.'.Gpf_Db_Table_Countries::COUNTRY_CODE);
        $this->addDataColumn(Gpf_Db_Table_Countries::COUNTRY_CODE, 'c.' . Gpf_Db_Table_Countries::COUNTRY_CODE);
        $this->addDataColumn(Gpf_Db_Table_Countries::COUNTRY, 'c.' . Gpf_Db_Table_Countries::COUNTRY);
        $this->initStatColumns();
    }

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_Countries::COUNTRY, $this->_("Country"), true);
        $this->addViewColumn('impressionsRaw', $this->_("Impressions (Raw)"), true);
        $this->addViewColumn('impressionsUnique', $this->_("Impressions (Unique)"), true);
        $this->addViewColumn('clicksRaw', $this->_("Clicks (Raw)"), true);
        $this->addViewColumn('clicksUnique', $this->_("Clicks (Unique)"), true);
        $this->addViewColumn('ctrRaw', $this->_("CTR (Raw)"), true);
        $this->addViewColumn('ctrUnique', $this->_("CTR (Unique)"), true);
        $this->addViewColumn('salesCount', $this->_("Sales Count"), true);
        $this->addViewColumn('salesTotal', $this->_("Total Revenue"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('scrRaw', $this->_("SCR (Raw)"), true);
        $this->addViewColumn('scrUnique', $this->_("SCR (Unique)"), true);
        $this->addViewColumn('commissions', $this->_("Commissions"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('avgAmountOfOrder', $this->_("Avg Order"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_Countries::COUNTRY, '100');

        $this->addDefaultViewColumn('salesCount', '20', 'D');
        $this->addDefaultViewColumn('clicksRaw', '20');
    }

    protected function initRequiredColumns() {
        parent::initRequiredColumns();
        $this->addRequiredColumn(Gpf_Db_Table_Countries::COUNTRY_CODE);
        $this->addRequiredColumn(Gpf_Db_Table_Countries::COUNTRY);
        $this->addRequiredColumn('salesTotal');
        $this->addRequiredColumn('impressionsRaw');
        $this->addRequiredColumn('commissions');
    }


    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_Countries::getName(), 'c');
        $this->buildStatsFrom();
    }

    /**
     * Returns row data for grid
     * @service mapoverlay read
     * @param Gpf_Data_RecordSet $columns
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service mapoverlay export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }

    /**
     * @service mapoverlay read
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        return parent::getRowCount($params);
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $params = parent::getStatsParameters();
        return $this->addParamsWithDateRangeFilter($params);
    }
}
?>
