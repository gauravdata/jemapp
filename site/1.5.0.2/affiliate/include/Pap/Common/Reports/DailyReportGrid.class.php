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
class Pap_Common_Reports_DailyReportGrid extends Pap_Common_StatsGrid {

    /**
     * @var Gpf_DateTime_Range
     */
    private $range;

    public function __construct() {
        parent::__construct(
            'DATE('.Gpf_Common_DateUtils::getSqlTimeZoneColumn(Pap_Stats_Table::DATEINSERTED).')', 'ds',
            'day');
    }

    protected function initViewColumns() {
        $this->addViewColumn('id', $this->_("Day"), true);
        $this->addViewColumn("impressionsRaw", $this->_("Impressions raw"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn("impressionsUnique", $this->_("Impressions unique"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn("clicksRaw", $this->_("Clicks raw"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn("clicksUnique", $this->_("Clicks unique"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn("ctrRaw", $this->_("CTR raw"), true, Gpf_View_ViewColumn::TYPE_PERCENTAGE);
        $this->addViewColumn("ctrUnique", $this->_("CTR unique"), true, Gpf_View_ViewColumn::TYPE_PERCENTAGE);
        $this->addViewColumn("salesCount", $this->_("Sales count"), true, Gpf_View_ViewColumn::TYPE_NUMBER);
        $this->addViewColumn("salesTotal", $this->_("Sales total"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn("commissions", $this->_("Commissions"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addAllActionsViewColumns();
    }

    /**
     * @return Gpf_DateTime_Range
     */
    protected function getRange() {
        if ($this->range == null) {
            $this->initRange();
        }
        return $this->range;
    }

    private function initRange() {
        if (($month = $this->filters->getFilterValue('month')) == '') {
            $month = date('n');
        }
        if (($year = $this->filters->getFilterValue('year')) == '') {
            $year = date('Y');
        }
        $date = new Gpf_DateTime("$year-$month-10");
        $this->range = new Gpf_DateTime_Range($date->getMonthStart(), $date->getMonthEnd());
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn("ds.day");
        $this->initStatColumns();
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn("id", '', 'A');
        $this->addDefaultViewColumn("impressionsRaw", '', 'N');
        $this->addDefaultViewColumn("clicksRaw", '', 'N');
        $this->addDefaultViewColumn("salesCount", '', 'N');
        $this->addDefaultViewColumn("commissions", '', 'N');
    }
     
    protected function buildFrom() {
        $this->_selectBuilder->from->addSubselect($this->getDaysSelect(), 'ds');
        $this->buildStatsFrom();
    }

    protected function buildWhere() {
        $this->_selectBuilder->where->add('day', '<=', $this->getRange()->getTo()->getDay());
        parent::buildWhere();
    }

    /**
     * @deprecated
     * @param Gpf_Data_RecordSet $inputResult
     * @return Gpf_Data_RecordSet
     */
    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
        $sumRecord = $inputResult->createRecord();
        $sumRecord->set("id", $this->_('Total'));
        foreach ($inputResult as $record) {
            foreach ($record as $name => $value) {
                if ($name == "id") {
                    continue;
                }
                if (strstr($name, "ctr")) {
                    $sumRecord->set($name, '--');
                    continue;
                }
                $sumRecord->set($name, $sumRecord->get($name) + $value);
            }
        }
        $inputResult->add($sumRecord);
        return $inputResult;
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $statsParams = parent::getStatsParameters();
        $statsParams->setRange($this->getRange());
        return $this->addParamsWithDateRangeFilter($statsParams);
    }

    /**
     * @service daily_report read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    protected function buildLimit() {
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function getDaysSelect() {
        $daysSelect = new Gpf_SqlBuilder_SelectBuilder();
        $daysSelect->select->add("STR_TO_DATE(CONCAT('".$this->getRange()->getFrom()->getYear()."-".$this->getRange()->getFrom()->getMonth()."-', m.day), '%Y-%m-%d')", 'day');
        $daysSelect->from->add('(' . $this->getDaysSubSelect() . ')', 'm');
        $daysSelect->where->add('m.day', '<=', $this->getRange()->getTo()->getDay());
        return $daysSelect;
    }

    private function getDaysSubSelect() {
        $subSelect = 'SELECT 1 AS day';
        for ($i = 2; $i <= 31; $i++) {
            $subSelect .= ' UNION SELECT ' . $i . ' AS day';
        }
        return $subSelect;
    }
}
?>
