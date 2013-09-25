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
abstract class Pap_Common_StatsGrid extends Gpf_View_GridService {

    protected $statColumns = array();
    protected $groupColumn;
    protected $mainTablePreffix;
    protected $mainTableColumn;
    protected $columnsToDatabaseNames = array();

    const GROUP_COLUMN_ALIAS = 'groupColumn';

    const IMPRESSIONS_RAW = 'impressionsRaw';
    const IMPRESSIONS_UNIQUE = 'impressionsUnique';
    const CLICKS_RAW = 'clicksRaw';
    const CLICKS_UNIQUE = 'clicksUnique';
    const CTR_RAW = 'ctrRaw';
    const CTR_UNIQUE = 'ctrUnique';
    const SALES_COUNT = 'salesCount';
    const SALES_TOTAL = 'salesTotal';
    const COMMISSIONS = 'commissions';
    const SCR_RAW = 'scrRaw';
    const SCR_UNIQUE = 'scrUnique';
    const AVG_COMMISSION_PER_CLICK = 'avgCommissionPerClick';
    const AVG_COMMISSION_PER_IMP = 'avgCommissionPerImp';
    const AVG_AMOUNT_OF_ORDER = 'avgAmountOfOrder';

    public function __construct($groupColumn, $mainTablePreffix, $mainTableColumn = null) {
        $this->groupColumn = $groupColumn;
        $this->mainTablePreffix = $mainTablePreffix;
        $this->mainTableColumn = $mainTableColumn;
        if (is_null($mainTableColumn)) {
            $this->mainTableColumn = $groupColumn;
        }

        // DB column names
        $raw = Pap_Db_Table_ClicksImpressions::RAW;
        $unique = Pap_Db_Table_ClicksImpressions::UNIQUE;
        $count = 'count';
        $commission = Pap_Db_Table_Transactions::COMMISSION;
        $totalCost = Pap_Db_Table_Transactions::TOTAL_COST;

        $this->statColumns[self::IMPRESSIONS_RAW] = "IF(im.$raw is NULL, 0, im.$raw)";
        $this->statColumns[self::IMPRESSIONS_UNIQUE] = "IF(im.$unique is NULL, 0, im.$unique)";

        $this->statColumns[self::CLICKS_RAW] = "IF(cl.$raw is NULL, 0, cl.$raw)";
        $this->statColumns[self::CLICKS_UNIQUE] = "IF(cl.$unique is NULL, 0, cl.$unique)";

        $this->statColumns[self::CTR_RAW] = "IF(im.$raw>0 AND cl.$raw>0, (cl.$raw/im.$raw)*100, 0)";
        $this->statColumns[self::CTR_UNIQUE] = "IF(im.$unique>0 AND cl.$unique>0, (cl.$unique/im.$unique)*100, 0)";

        $this->statColumns[self::SALES_COUNT] = "IF(tr.$count is NULL, 0, tr.$count)";
        $this->statColumns[self::SALES_TOTAL] = "IF(tr.$totalCost is NULL, 0, tr.$totalCost)";
        $this->statColumns[self::COMMISSIONS] = "IF(tr.$commission is NULL, 0, tr.$commission)";

        $this->statColumns[self::SCR_RAW] = "IF(cl.$raw>0 AND tr.$count>0, (tr.$count/cl.$raw)*100, 0)";
        $this->statColumns[self::SCR_UNIQUE] = "IF(cl.$unique>0 AND tr.$count>0, (tr.$count/cl.$unique)*100, 0)";

        $this->statColumns[self::AVG_COMMISSION_PER_CLICK] = "IF(cl.$raw>0 AND tr.$commission>0, tr.$commission/cl.$raw, 0)";
        $this->statColumns[self::AVG_COMMISSION_PER_IMP] = "IF(im.$raw>0 AND tr.$commission>0, tr.$commission/im.$raw, 0)";
        $this->statColumns[self::AVG_AMOUNT_OF_ORDER] = "IF(tr.$count>0 AND tr.$commission>0, tr.$commission/tr.$count, 0)";

        $context = new Pap_Common_StatsColumnsContext($this->statColumns);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Common_StatsGrid.initStatsColumns', $context);
        $this->statColumns = $context->getStatsColumns();

        $this->columnsToDatabaseNames[self::SALES_COUNT] = $count;
        $this->columnsToDatabaseNames[self::SALES_TOTAL] = $totalCost;
        $this->columnsToDatabaseNames[self::COMMISSIONS] = $commission;

        parent::__construct();
    }


    public function getGroupColumn() {
        return $this->groupColumn;
    }

    public function getMainTablePrefix() {
        return $this->mainTablePreffix;
    }

    public function getMainTableColumn() {
        return $this->mainTableColumn;
    }

    protected function initStatColumns() {
        foreach ($this->statColumns as $alias => $sql) {
            $this->addDataColumn($alias, $sql);
        }
    }

    protected function addAllActionsViewColumns() {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Common_StatsGrid.addAllActionsViewColumns', $this);
    }

    protected function addSelect($sql, $alias) {
        if (array_key_exists($alias, $this->statColumns) && !$this->isColumnRequired($alias)) {
            return;
        }
        parent::addSelect($sql, $alias);
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    protected function initResult() {
        $result = new Gpf_Data_RecordSet();
        foreach ($this->dataColumns as $column) {            
            if (array_key_exists($column->getId(), $this->statColumns) && !$this->isColumnRequired($column->getId())) {
                continue;
            }
            if ($this->isColumnRequired($column->getId()) || $this->filters->isFilter($column->getId())) {
                 $result->getHeader()->add($column->getId());
            }
        }
        return $result;
    }
    
    protected function buildSelect() {
        foreach ($this->dataColumns as $column) {
            if ($this->isColumnRequired($column->getId()) || $this->filters->isFilter($column->getId())) {
                $this->addSelect($column->getName(), $column->getId());
            }
        }
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $params = new Pap_Stats_Params();
        $params->initFrom($this->filters);
        if (!$params->isStatusDefined()) {
            $params->setStatus(Pap_Common_Constants::STATUS_APPROVED);
        }
        return $params;
    }

    protected function areColumnsRequiredOrInFilter(array $columnNames) {
        return $this->areColumnsRequired($columnNames) || $this->areColumnsInFilter($columnNames);
    }

    private function areColumnsInFilter(array $columnNames) {
        foreach ($columnNames as $columnName) {
            if ($this->filters->isFilter($columnName)) {
                return true;
            }
        }
        return false;
    }

    public function areColumnsRequired(array $columnNames) {
        foreach ($columnNames as $columnName) {
            if ($this->isColumnRequired($columnName)) {
                return true;
            }
        }
        return false;
    }

    protected function buildStatsFrom() {
        $statParams = $this->getStatsParameters();

        if ($this->areColumnsRequiredOrInFilter(array(self::CLICKS_RAW, self::CLICKS_UNIQUE, self::CTR_RAW, self::CTR_UNIQUE, self::SCR_RAW, self::SCR_UNIQUE, self::AVG_COMMISSION_PER_CLICK))) {
            $clickSelect = Pap_Db_Table_Clicks::getInstance()->getStatsSelect($statParams, $this->groupColumn, self::GROUP_COLUMN_ALIAS);
            $this->_selectBuilder->from->addLeftJoin('('.$clickSelect->toString().')',
                    'cl', 'cl.'.self::GROUP_COLUMN_ALIAS.'='.$this->mainTablePreffix. '.'. $this->mainTableColumn);
        }

        if ($this->areColumnsRequiredOrInFilter(array(self::IMPRESSIONS_RAW, self::IMPRESSIONS_UNIQUE, self::CTR_RAW, self::CTR_UNIQUE, self::AVG_COMMISSION_PER_IMP))) {
            $impSelect = Pap_Db_Table_Impressions::getInstance()->getStatsSelect($statParams, $this->groupColumn, self::GROUP_COLUMN_ALIAS);
            $this->_selectBuilder->from->addLeftJoin('('.$impSelect->toString().')',
                    'im', 'im.'.self::GROUP_COLUMN_ALIAS.'='.$this->mainTablePreffix. '.'. $this->mainTableColumn);
        }

        if ($this->areColumnsRequiredOrInFilter(array(self::SALES_COUNT, self::SALES_TOTAL, self::COMMISSIONS, self::SCR_RAW, self::SCR_UNIQUE, self::AVG_COMMISSION_PER_CLICK, self::AVG_COMMISSION_PER_IMP, self::AVG_AMOUNT_OF_ORDER))) {
            $transSelect = $this->getTransactionStatsSelect($statParams);
            $this->addJoinSelectToSelectBuilder($transSelect, 'tr');
        }

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Common_StatsGrid.buildStatsFrom',
        new Pap_Common_StatsGridParamsContext($this->_selectBuilder, $this, $statParams));
    }

    /**
     * @param Pap_Stats_Params $params
     * @return Pap_Stats_Params
     */
    protected function addParamsWithDateRangeFilter(Pap_Stats_Params $params, $dateFilterName = 'statsdaterange') {
        $dateRange = Array();
        $filters = $this->filters->getFilter($dateFilterName);

        foreach ($filters as $filter) {
            $dateRange = $filter->addDateValueToArray($dateRange);
        }

        if (array_key_exists('dateFrom', $dateRange)) {
            $params->setDateFrom(new Gpf_DateTime($dateRange['dateFrom']));
        }
        if (array_key_exists('dateTo', $dateRange)) {
            $params->setDateTo(new Gpf_DateTime($dateRange['dateTo']));
        }
        return $params;
    }

    /**
     * @param Pap_Stats_Params $statParams
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function getTransactionStatsSelect(Pap_Stats_Params $statParams) {
        $transactionStats = new Pap_Stats_Computer_TransactionsStatsBuilder($statParams, $this->groupColumn, self::GROUP_COLUMN_ALIAS);
        $transactionStats->getTransactionsWhereClause()->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_SALE);
        return $transactionStats->getStatsSelect();
    }

    /**
     * @param Pap_Stats_Params $statParams
     * @param $tableAlias
     */
    protected function addJoinSelectToSelectBuilder(Gpf_SqlBuilder_SelectBuilder $select, $tableAlias) {
        $this->_selectBuilder->from->addLeftJoin('('.$select->toString().')',
                    $tableAlias, $tableAlias.'.'.self::GROUP_COLUMN_ALIAS.'='.$this->mainTablePreffix. '.'. $this->mainTableColumn);
    }

    public function addStatCustomFilterFields(Gpf_View_CustomFilterFields $filterFields) {
        $filterFields->addNumberField(self::IMPRESSIONS_RAW, $this->_("Raw impressions"));
        $filterFields->addNumberField(self::IMPRESSIONS_UNIQUE, $this->_("Unique impressions"));
        $filterFields->addNumberField(self::CLICKS_RAW, $this->_("Raw clicks"));
        $filterFields->addNumberField(self::CLICKS_UNIQUE, $this->_("Unique clicks"));
        $filterFields->addNumberField(self::CTR_RAW, $this->_("Clickthrough ratio raw"));
        $filterFields->addNumberField(self::CTR_UNIQUE, $this->_("Clickthrough ratio unique"));
        $filterFields->addNumberField(self::SALES_COUNT, $this->_("Sales count"));
        $filterFields->addNumberField(self::SALES_TOTAL, $this->_("Sale revenue"));
        $filterFields->addNumberField(self::COMMISSIONS, $this->_("Commissions"));
        $filterFields->addNumberField(self::SCR_RAW, $this->_("Conversion ratio raw"));
        $filterFields->addNumberField(self::SCR_UNIQUE, $this->_("Conversion ratio unique"));
        $filterFields->addNumberField(self::AVG_COMMISSION_PER_CLICK, $this->_("Average commission per click"));
        $filterFields->addNumberField(self::AVG_COMMISSION_PER_IMP, $this->_("Average commission per impression"));
        $filterFields->addNumberField(self::AVG_AMOUNT_OF_ORDER, $this->_("Average amount of order"));
    }
}
?>
