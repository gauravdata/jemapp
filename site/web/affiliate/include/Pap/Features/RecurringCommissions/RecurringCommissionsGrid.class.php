<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 22049 2008-11-01 13:31:20Z aharsani $
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
class Pap_Features_RecurringCommissions_RecurringCommissionsGrid extends Pap_Common_Reports_TransactionsGridBase implements Gpf_View_Grid_HasRowFilter {
    const COLUMN_AFFILIATE = 'userid';
    const COLUMN_TOTALCOST = 'totalcost';
    const COLUMN_ORDERID = 'orderid';
    const COLUMN_DATECREATED = 'datecreated';
    const COLUMN_LASTCOMMISSION = 'datelastcommission';
    const COLUMN_STATUS = 'rstatus';
    const COLUMN_RECURRENCE = 'recurrencepresetid';
    const COLUMN_RECURRENCE_NAME = 'recurrencepresetname';
    
    protected function initViewColumns() {
        $this->addViewColumn(self::COLUMN_AFFILIATE, $this->_("Affiliate"), true);
        $this->addViewColumn(self::COLUMN_TOTALCOST, $this->_("Total cost"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn(self::COLUMN_ORDERID, $this->_("Order ID"), true);
        $this->addViewColumn(self::COLUMN_DATECREATED, $this->_("Date created"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(self::COLUMN_LASTCOMMISSION, $this->_("Last commission"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(self::COLUMN_STATUS, $this->_("Status"), true);
        $this->addViewColumn(self::COLUMN_RECURRENCE_NAME, $this->_("Recurrence"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"));
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn('rc.'.Pap_Db_Table_RecurringCommissions::ID);
        $this->addDataColumn(self::COLUMN_AFFILIATE,               'pu.'.Pap_Db_Table_Transactions::USER_ID);
        $this->addDataColumn('username',                           'au.'.Gpf_Db_Table_AuthUsers::USERNAME);
        $this->addDataColumn('firstname',                          'au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $this->addDataColumn('lastname',                           'au.'.Gpf_Db_Table_AuthUsers::LASTNAME);
        $this->addDataColumn(self::COLUMN_TOTALCOST, 't.'.Pap_Db_Table_Transactions::TOTAL_COST);
        $this->addDataColumn(self::COLUMN_ORDERID, 't.'.Pap_Db_Table_Transactions::ORDER_ID);
        $this->addDataColumn(self::COLUMN_DATECREATED, 't.'.Pap_Db_Table_Transactions::DATE_INSERTED);
        $this->addDataColumn(self::COLUMN_LASTCOMMISSION, 'rc.'.Pap_Db_Table_RecurringCommissions::LAST_COMMISSION_DATE);
        $this->addDataColumn(self::COLUMN_STATUS, 'rc.'.Pap_Db_Table_RecurringCommissions::STATUS);
        $this->addDataColumn(self::COLUMN_RECURRENCE_NAME, 'rp.'.Gpf_Db_Table_RecurrencePresets::NAME);
        $this->addDataColumn(self::COLUMN_RECURRENCE, 'rp.'.Gpf_Db_Table_RecurrencePresets::ID);
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn(self::COLUMN_AFFILIATE);
        $this->addDefaultViewColumn(self::COLUMN_TOTALCOST);
        $this->addDefaultViewColumn(self::COLUMN_ORDERID);
        $this->addDefaultViewColumn(self::COLUMN_STATUS);
        $this->addDefaultViewColumn(self::COLUMN_RECURRENCE_NAME);
        $this->addDefaultViewColumn(self::COLUMN_DATECREATED);
        $this->addDefaultViewColumn(self::COLUMN_LASTCOMMISSION);
        $this->addDefaultViewColumn(self::ACTIONS);
    }

    function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_RecurringCommissions::getName(), 'rc');
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_RecurringCommissionEntries::getName(),
            "rce", "rce.recurringcommissionid = rc.recurringcommissionid AND rce.tier = 1");
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Users::getName(), "pu", "rce.userid = pu.userid");
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
        
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Transactions::getName(), "t", "t.transid = rc.transid");
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_RecurrencePresets::getName(), 'rp', 'rp.recurrencepresetid = rc.recurrencepresetid');
    }

    protected function doMossoHack() {
    }

    /**
     * @param $row
     * @return DataRow or null
     */
    public function filterRow(Gpf_Data_Row $row) {
       $row->set(self::COLUMN_RECURRENCE_NAME, $this->_localize($row->get(self::COLUMN_RECURRENCE_NAME)));
       return $row;
    }
    
    /**
     * @service recurring_transaction read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service recurring_transaction export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
    
    /**
     * @service recurring_transaction read
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        return parent::getRowCount($params);
    }
}
?>
