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

require_once 'OFC/OFC_Chart.php';

/**
 * @package PostAffiliatePro
 */
class Pap_Common_Reports_StatisticsBase extends Gpf_Object {

    const DEFAULT_DATA_TYPE = 'saleCount';
    
    /**
     * @var Pap_Stats_Params
     */
    private $statsParameters;

    private $chartType;
    private $timeGroupBy;
    private $campaignId;
    
    /**
     * @var Pap_Common_Reports_Chart_DataType
     */
    private $dataType1;
    /**
     * @var Pap_Common_Reports_Chart_DataType
     */
    private $dataType2;
    
    
    /**
     * @var array<Pap_Common_Reports_Chart_DataType>
     */
    private $dataTypes;
    
    public function __construct() {
    }

    /**
     * returns data for chart
     *
     * @service trend_stats read
     * @param $fields
     * @return Gpf_Rpc_Chart
     */
    public function loadData(Gpf_Rpc_Params $params) {
        $this->initCampaignId($params);
        $this->init($params);
        if ($this->dataType2 == null) {
            return new Gpf_Rpc_ChartResponse($this->buildChart(), $this->dataType1->getId());
        }
        return new Gpf_Rpc_ChartResponse($this->buildChart(), $this->dataType1->getId(), $this->dataType2->getId());
    }
    
    /**
     * returns data for chart
     *
     * @service trend_stats read
     * @param $fields
     * @return Gpf_Rpc_Chart
     */
    public function loadDataTypes(Gpf_Rpc_Params $params) {
        $this->initCampaignId($params);
        $this->initDataTypes();
        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->setHeader(array("id", "value"));
        foreach ($this->dataTypes as $dataType) {
            $recordSet->add(array($dataType->getId(), $dataType->getName()));
        }
        return $recordSet;
    }
    
    public function addDataType(Pap_Common_Reports_Chart_DataType $dataType) {
        $this->dataTypes[$dataType->getId()] = $dataType;
    }
    
    public function clearDataTypes() {
        $this->dataTypes = array();
    }
    
    public function getCampaignId() {
        return $this->campaignId;
    }
    
    private function initCampaignId(Gpf_Rpc_Params $params) {
        $filterCollection = new Gpf_Rpc_FilterCollection($params);
        $this->campaignId = $filterCollection->getFilterValue('campaignid');
        if ($this->campaignId == '') {
            $this->campaignId = null;
        }
    }
    
    private function initDataTypes() {
        if ($this->dataTypes != null) {
            return;
        }
        $this->clearDataTypes();
        
        $this->addDataType(new Pap_Common_Reports_Chart_ImpressionDataType());
        
        $this->addDataType(new Pap_Common_Reports_Chart_ClickDataType($this->_('Number of Clicks')));
        
        $this->addDataType(new Pap_Common_Reports_Chart_TransactionDataType(
            'commission', $this->_('All Commissions'), Pap_Stats_Computer_Graph_Transactions::COMMISSION));
        
        $this->addDataType(new Pap_Common_Reports_Chart_TransactionDataType(
            'saleCount', $this->_('Number of Sales'), Pap_Stats_Computer_Graph_Transactions::COUNT, Pap_Common_Constants::TYPE_SALE));
        $this->addDataType(new Pap_Common_Reports_Chart_TransactionDataType(
            'saleCommission', $this->_('Commission of Sales'), Pap_Stats_Computer_Graph_Transactions::COMMISSION, Pap_Common_Constants::TYPE_SALE));
        $this->addDataType(new Pap_Common_Reports_Chart_TransactionDataType(
            'saleTotalCost', $this->_('Revenue of Sales'), Pap_Stats_Computer_Graph_Transactions::TOTALCOST, Pap_Common_Constants::TYPE_SALE));
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.StatisticsBase.initDataTypes', $this);
    }

    /**
     * @param Gpf_Rpc_Params $params
     * @return Pap_Stats_Params
     */
    private function init(Gpf_Rpc_Params $params) {
        $filterCollection = new Gpf_Rpc_FilterCollection($params);

        $this->chartType = $filterCollection->getFilterValue("chartType");
        $this->timeGroupBy = $filterCollection->getFilterValue("groupBy");

        try {
            $this->dataType1 = $this->getDataType($filterCollection->getFilterValue("dataType1"));
        } catch (Gpf_Exception $e) {
            $this->dataType1 = $this->getDataType($this->getDefaultDataType());
        }

        try {
            $this->dataType2 = $this->getDataType($filterCollection->getFilterValue("dataType2"));
        } catch (Gpf_Exception $e) {
            $this->dataType2 = null;
        }
        
        $this->initStatParams($filterCollection);
    }
    
    private function getDefaultDataType() {
        $context = new Gpf_Plugins_ValueContext(self::DEFAULT_DATA_TYPE);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.StatisticsBase.getDefaultDataType', $context);
        return $context->get();
    }

    private function initStatParams(Gpf_Rpc_FilterCollection $filterCollection) {
        $this->statsParameters = new Pap_Stats_Params();
        $this->statsParameters->initFrom($filterCollection);
        
        if (!$this->statsParameters->isDateFromDefined() || !$this->statsParameters->isDateToDefined()) {
            throw new Gpf_Exception("Date filter must be set in StatisticsBase class");
        }
        
        if (!$this->statsParameters->isStatusDefined()) {
            $this->statsParameters->setStatus(Pap_Common_Constants::STATUS_APPROVED);
        }
    }

    private function buildChart() {
        $chart = new Gpf_Rpc_Chart();
        $chart->setChartType($this->chartType);
        $labels = new Gpf_Chart_Labels($this->statsParameters->getDateFrom(), $this->statsParameters->getDateTo(), $this->timeGroupBy);
        $chart->setLabels($labels->getLabels());

        $computer = $this->dataType1->getComputer($this->statsParameters, $this->timeGroupBy);
        $computer->computeStats();
        $chartData = $this->createChartData($this->dataType1, $chart->getLineColor(1));
        $chartData->fill($labels, $computer->getResult());
        $chart->addData1Recordset($chartData);

        if ($this->dataType2 !== null) {
            $computer = $this->dataType2->getComputer($this->statsParameters, $this->timeGroupBy);
            $computer->computeStats();
            $chartData = $this->createChartData($this->dataType2, $chart->getLineColor(2));
            $chartData->fill($labels, $computer->getResult());
            $chart->addData2Recordset($chartData);
        }
        return $chart;
    }

    /**
     * @return Pap_Common_Reports_Chart_DataType
     */
    private function getDataType($dataType) {
        $this->initDataTypes();
        
        if (array_key_exists($dataType, $this->dataTypes)) {
            return $this->dataTypes[$dataType];
        }
        throw new Gpf_Exception('Unknown data type "'.$dataType.'" in Pap_Common_Reports_StatisticsBase');
    }

    private function createChartData(Pap_Common_Reports_Chart_DataType $dataType, $lineColor) {
        $chartData = new Gpf_Chart_DataRecordSet($dataType->getName(), $lineColor);
        $chartData->setTooltip($dataType->getTooltip());
        return $chartData;
    }

    protected function setAffiliateId(Pap_Stats_Params $statsParameters, $userId) {
        $statsParameters->setAffiliateId($userId);
    }
}

?>
