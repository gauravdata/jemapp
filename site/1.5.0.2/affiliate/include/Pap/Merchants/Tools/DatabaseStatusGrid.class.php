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
class Pap_Merchants_Tools_DatabaseStatusGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn("tableName", $this->_("Table name"), true);
        $this->addViewColumn("recordsCount", $this->_("Records"), true);
        $this->addViewColumn("totalSize", $this->_("Total size"), true);
        $this->addViewColumn("dataSize", $this->_("Data size"), true);
        $this->addViewColumn("indexSize", $this->_("Index size"), true);
        $this->addViewColumn(parent::ACTIONS, $this->_("Actions"), true);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn("TABLE_NAME");
        $this->addDataColumn("tableName", "TABLE_NAME");
        $this->addDataColumn("recordsCount", "TABLE_ROWS");
        $this->addDataColumn("totalSize", "(DATA_LENGTH + INDEX_LENGTH)");
        $this->addDataColumn("dataSize", "DATA_LENGTH");
        $this->addDataColumn("indexSize", "INDEX_LENGTH");
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn("tableName", '', 'A');
        $this->addDefaultViewColumn("recordsCount", '', 'N');
        $this->addDefaultViewColumn("recordsCount", '', 'N');
        $this->addDefaultViewColumn("totalSize", '', 'N');
        $this->addDefaultViewColumn("dataSize", '', 'N');
        $this->addDefaultViewColumn("indexSize", '', 'N');
        $this->addDefaultViewColumn(parent::ACTIONS, '', 'N');
    }

    function buildFrom() {
        $this->_selectBuilder->from->add("INFORMATION_SCHEMA.TABLES");
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add("TABLE_SCHEMA", "=", Gpf_Settings::get(Gpf_Settings_Gpf::DB_DATABASE));
    }

    /**
     * Analyze table with tableName
     *
     * @service database analyze
     * @param Gpf_Rpc_Params $params (tableName)
     */
    public function tableAnalyze(Gpf_Rpc_Params $params) {
        return $this->maintenanceTable($params, Gpf_SqlBuilder_DBMaintenance::ANALYZE_TABLE);
    }

    /**
     * Optimize table with tableName
     *
     * @service database optimize
     * @param Gpf_Rpc_Params $params (tableName)
     */
    public function tableOptimize(Gpf_Rpc_Params $params) {
    	return $this->maintenanceTable($params, Gpf_SqlBuilder_DBMaintenance::OPTIMIZE_TABLE);
    }

    /**
     * Repair table with tableName
     *
     * @service database repair
     * @param Gpf_Rpc_Params $params (tableName)
     */
    public function tableRepair(Gpf_Rpc_Params $params) {
    	return $this->maintenanceTable($params, Gpf_SqlBuilder_DBMaintenance::REPAIR_TABLE);
    }

    /**
     * 
     * @param $params
     * @param $option
     * @return Gpf_Rpc_Action
     */
    private function maintenanceTable(Gpf_Rpc_Params $params, $option) {
    	$action = new Gpf_Rpc_Action($params);
        $dbMaintenance = new Gpf_SqlBuilder_DBMaintenance();

        try {
           $dbMaintenance->addTable($action->getParam("tableName"));
           $record = $dbMaintenance->maintenanceOne($option);
           $action->setInfoMessage($this->_("Operation")." ".$this->_($record->get("Op")." table ".$action->getParam("tableName")." finished with ".$record->get("Msg_type").": ".$record->get("Msg_text")));
           $action->addOk();
        } catch (Gpf_Exception $e) {
            $action->setErrorMessage($this->_($e));
            $action->addError();
        }

        return $action;
    }

    /**
     * @service database read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service database export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
