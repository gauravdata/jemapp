<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
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
abstract class Pap_Stats_Computer_Graph_Base extends Pap_Stats_Computer_Base {
    
    private $timeGroupBy;
    private $result = array();
    
    public function __construct(Pap_Stats_Table $table, Pap_Stats_Params $params, $timeGroupBy) {
        parent::__construct($table, $params);
        $this->timeGroupBy = $timeGroupBy;
    }
    
    protected function initSelectClause() {
        $this->selectBuilder->select->add('MIN(' . Gpf_Common_DateUtils::getSqlTimeZoneColumn('t.'.Pap_Stats_Table::DATEINSERTED) . ')', "datetime");
    }
    
    protected function initGroupBy() {
        $this->selectBuilder->groupBy->add($this->getSqlGroupBy());
    }
    
    private function getSqlGroupBy() {
        $zoneColumnName = Gpf_Common_DateUtils::getSqlTimeZoneColumn('t.'.Pap_Stats_Table::DATEINSERTED);
        switch($this->timeGroupBy) {
            case Gpf_Common_DateUtils::DAY: return "TO_DAYS($zoneColumnName)";
            case Gpf_Common_DateUtils::WEEK: return "YEAR($zoneColumnName), WEEK($zoneColumnName)";
            case Gpf_Common_DateUtils::MONTH: return "YEAR($zoneColumnName), MONTH($zoneColumnName)";
            case Gpf_Common_DateUtils::YEAR: return "YEAR($zoneColumnName)";
        }
    }
    
    protected function convertTimeColumn($dateTime) {
        return Gpf_Common_DateUtils::formatByUnit(new Gpf_DateTime($dateTime), $this->timeGroupBy);
    }
    
    /**
     * @return array
     */
    public function getResult() {
        return $this->result;
    }
    
    protected function processResult() {
        foreach ($this->selectBuilder->getAllRowsIterator() as $resultRow) {
            $this->processRow($resultRow);
        }
    }
    
    private function processRow(Gpf_Data_Record $dataRow) {
        $time = $dataRow->get("datetime");
        $value = $dataRow->get("value");
        $this->result[$this->convertTimeColumn($time)] = $value;
    }
}
?>
