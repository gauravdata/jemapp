<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: CurrenciesGrid.class.php 22049 2008-11-01 13:31:20Z aharsani $
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
class Pap_Features_MultipleCurrency_CurrenciesGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn('symbol', $this->_("Symbol"), false);
        $this->addViewColumn('cprecision', $this->_("Precision"), false);
        $this->addViewColumn('wheredisplay', $this->_("Where to display"), false);
        $this->addViewColumn('exchrate', $this->_("Exchange rate"), false);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Currencies::ID);
        $this->addDataColumn('name', 'name');
        $this->addDataColumn('symbol', 'symbol');
        $this->addDataColumn('cprecision', 'cprecision');
        $this->addDataColumn('wheredisplay', 'wheredisplay');
        $this->addDataColumn('exchrate', 'exchrate');
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn('name', '40px', 'N');
        $this->addDefaultViewColumn('symbol', '40px', 'N');
        $this->addDefaultViewColumn('cprecision', '40px', 'N');
        $this->addDefaultViewColumn('wheredisplay', '40px', 'N');
        $this->addDefaultViewColumn('exchrate', '40px', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
    }
    
    protected function buildFrom() {
      $this->_selectBuilder->from->add(Gpf_Db_Table_Currencies::getName());
    }
    
    protected function buildWhere() {
        parent::buildFilter();
        $this->_selectBuilder->where->add(Gpf_Db_Table_Currencies::IS_DEFAULT, "<>", 1);
    }
        
    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createEmptyRow(Gpf_Rpc_Params $params) {
        $row = new Gpf_Db_Currency();
        $row->set(Gpf_Db_Table_Accounts::ID, Gpf_Session::getAuthUser()->getAccountId());
        $row->set(Gpf_Db_Table_Currencies::NAME, $this->_("NEW"));
        $row->set(Gpf_Db_Table_Currencies::SYMBOL, "?");
        $row->set(Gpf_Db_Table_Currencies::EXCHANGERATE, "1");
        return $row;
    }
    
    /**
     * @service currency read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service currency add
     * @return Gpf_Rpc_Serializable
     */
    public function getRowsAddNew(Gpf_Rpc_Params $params) {
        return parent::getRowsAddNew($params);
    }
    
    /**
     * @service currency export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
