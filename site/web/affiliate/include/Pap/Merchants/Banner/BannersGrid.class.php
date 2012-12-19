<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
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
class Pap_Merchants_Banner_BannersGrid extends Pap_Common_Banner_BannersGrid implements Gpf_View_Grid_HasRowFilter {

    private $localLimit = null;
    private $localOffset = null;
    private $isBannerPreviewRequired = false;

    /**
     * Returns row data for grid
     * @service banner read
     * @param $filters
     * @param $limit
     * @param $offset
     * @param $sort_col
     * @param $sort_asc
     * @param Gpf_Data_RecordSet $columns
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $this->initParameters($params);
        return parent::getRows($params);
    }

    /**
     * @service banner export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        $this->initParameters($params);
        return parent::getCSVFile($params);
    }

    /**
     * @service banner read
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        return parent::getRowCount($params);
    }
    
    protected function initRequiredColumns() {
    	parent::initRequiredColumns();
    	$this->addRequiredColumn('bannerpreview');
    }

    protected function initResult() {
        $result = parent::initResult();
       	if ($this->isColumnRequired('bannerpreview')) {
       	    $result->addColumn('bannerpreview');
       	    $this->isBannerPreviewRequired = true;
       	}
        return $result;
    }

    /**
     * @param $row
     * @return DataRow or null
     */
    public function filterRow(Gpf_Data_Row $row) {
        // optimized for speed
        if ($this->isBannerPreviewRequired) {
            $row->add('bannerpreview', $this->createBannerPreview($row));
        }
        return $row;
    }
    
    /**
     * @return Pap_Common_Banner
     */
    protected function loadBannerObject(Gpf_Data_Record $record) {
        return $this->bannerFactory->getBannerFromRecord($record);
    }

    protected function createBannerPreview(Gpf_Data_Record $record) {
        try {
            $bannerObj = $this->loadBannerObject($record);
        } catch (Pap_Common_Banner_NotFound $e) {
            return $this->_('Unknown banner type');
        }
        try {
            return $bannerObj->getPreview($this->user);
        } catch (Exception $e) {
            Gpf_Log::error('Unable to generate preview for banner: ' . $bannerObj->getName());
            return '';
        }
    }

    private function getFilter($filterName, Gpf_Rpc_Data $data) {
        $filters = $data->getFilters()->getFilter($filterName);
        if (count($filters)>0) {
            return $filters[0]->getValue();
        }
        return null;
    }

    /**
     * @service banner read
     */
    public function getBannerPreview(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        $id = $this->getFilter('id', $data);
        $type = $this->getFilter('type', $data);
        $actualSize = $this->getFilter('actualSize', $data);
        try {
            $bannerObj = $this->bannerFactory->getBannerObject($id, $type);
        } catch (Pap_Common_Banner_NotFound $e) {
            $data->setValue('bannerPreview','Unknown banner type');
            return $data;
        }
        $bannerObj->setViewInActualSize($actualSize);
        $data->setValue("bannerPreview", $bannerObj->getPreview(new Pap_Common_User()));
        return $data;
    }

    protected function initViewColumns() {
        $this->addViewColumn("id", $this->_("ID"), false);
        $this->addViewColumn('name', $this->_("Banner name"), true);
        $this->addViewColumn('bannerpreview', $this->_("Banner preview"), false);
        $this->addViewColumn('campaignid', $this->_("Campaign"), true);
        $this->addViewColumn('destinationurl', $this->_("Target URL"), true);
        $this->addViewColumn('rtype', $this->_("Banner type"), true);
        $this->addViewColumn('rstatus', $this->_("Banner status"), true);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannersGrid.initViewColumns', $this);

        $this->addViewColumn('impressionsRaw', $this->_("Imps"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('clicksRaw', $this->_("Clicks"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('ctrRaw', $this->_("CTR"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('salesCount', $this->_("Sales"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('scrRaw', $this->_("SCR"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn('commissions', $this->_("Commissions"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);

        $this->addViewColumn('size', $this->_('Size'), true);
        $this->addViewColumn('description', $this->_('Description'), true);
        $this->addViewColumn('rorder', $this->_("Order"), true);
        $this->addViewColumn('dateinserted', $this->_("Date created"), true, Gpf_View_ViewColumn::TYPE_DATE);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        parent::initDataColumns();
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannersGrid.initDataColumns', $this);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('name', '100');
        $this->addDefaultViewColumn('destinationurl', '150');
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannersGrid.initDefaultView', $this);
        $this->addDefaultViewColumn('rorder', '20', 'A');
        $this->addDefaultViewColumn(self::ACTIONS, '30', 'N');
    }

    protected function buildFrom() {
        parent::buildFrom();
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyFrom',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder,
        new Gpf_Data_Record(array('joinedAlias', 'onJoinAlias'), array('a', 'b'))));
    }

    protected function buildWhere() {
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix'), array('b'))));
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $statsParameters = new Pap_Stats_Params();
        $statsParameters->setDateRange($this->statsDateParams["dateFrom"],
        $this->statsDateParams["dateTo"]);
        $statsParameters->setStatus(Pap_Common_Constants::STATUS_APPROVED);
        return $this->addParamsWithDateRangeFilter($statsParameters);
    }

    private function initParameters(Gpf_Rpc_Params $params) {
        $sortColumn = $params->get('sort_col');

        if(in_array($sortColumn, array('imps', 'clicks', 'ctr'))) {
            // for custom sorts disable offset and limit, we'll have to do it manualy
            $this->localLimit = $params->get('limit');
            if(!is_numeric($this->localLimit)) {
                $this->localLimit = 100;
            }
            $params->set('limit', 10000);

            $this->localOffset = $params->get('offset');
            if(!is_numeric($this->localOffset) || $this->localOffset < 0) {
                $this->localOffset = 0;
            }
            $params->set('offset', 0);
        }
    }

    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $preffixes = $select->where->getUniqueTablePreffixes();
        $preffixes['b'] = 'b';

        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from = clone $select->from;
        $count->from->prune($preffixes);
        $count->where = $select->where;
        $count->groupBy = $select->groupBy;
        $count->having = $select->having;
        return $count;
    }
}
?>
