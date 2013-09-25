<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: TransactionReportsGrid.class.php 16621 2008-03-21 09:37:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Affiliates_Reports_SubAffiliatesSalesGrid extends Gpf_View_GridService {
	
	private $userId;

    function __construct() {
        parent::__construct();
        $this->userId = Gpf_Session::getAuthUser()->getPapUserId();
    }

    protected function initViewColumns() {
        $this->addViewColumn("affiliate", $this->_("Affiliate"), true);
        $this->addViewColumn("dateinserted", $this->_("Date of registration"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn("totalSales", $this->_("% in total sales"), true);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_Transactions::TRANSACTION_ID, "tr.".Pap_Db_Table_Transactions::TRANSACTION_ID);
        $this->addDataColumn("dateinserted", "tr.".Pap_Db_Table_Transactions::DATE_INSERTED);
        $this->addDataColumn("totalSales", "SUM(tr.".Pap_Db_Table_Transactions::TOTAL_COST.")");
        $this->addDataColumn("affiliate", "au.".Gpf_Db_Table_AuthUsers::USERNAME);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn("affiliate", '40px', 'A');
        $this->addDefaultViewColumn("dateinserted", '40px', 'N');
        $this->addDefaultViewColumn("totalSales", '40px', 'N');
    }

    protected function buildFrom() {     
        $this->_selectBuilder->from->add(Pap_Db_Table_Transactions::getName(), "tr");   
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "tr.userid = pu.userid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
    }
    
    protected function buildWhere() {
    	$this->_selectBuilder->where->add( "pu.".Pap_Db_Table_Users::PARENTUSERID, "=", $this->userId);
    }
    
    protected function buildGroupBy() {
    	$this->_selectBuilder->groupBy->add("tr.".Pap_Db_Table_Transactions::USER_ID);
    }
    
    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
            
        }
    }
    
    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $this->_selectBuilder->where->add(Gpf_Db_Table_AuthUsers::USERNAME, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $this->_selectBuilder->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, 'LIKE', '%' . $filter->getValue() .'%', "OR");
    }
    
    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
    	$totalSales = $this->getTotalSales();
    	if ($totalSales > 0) {
    	   $onePercentValue = $totalSales / 100;
    	}
    	
        foreach ($inputResult as $record) {
            $percentInTotalSales = $record->get('totalSales') / $onePercentValue;
            $record->set('totalSales', $percentInTotalSales);
        }
    	
    	return $inputResult;
    }
    
    private function getTotalSales() {
    	$select = new Gpf_SqlBuilder_SelectBuilder();
    	$select->select->add("SUM(tr.".Pap_Db_Table_Transactions::TOTAL_COST.")", "totalSales");
    	$select->from->add(Pap_Db_Table_Transactions::getName(), "tr");   
        $select->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "tr.userid = pu.userid");
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
        $select->where->add( "pu.".Pap_Db_Table_Users::PARENTUSERID, "=", $this->userId);
        
        $result = $select->getOneRow();
        
        return $result->get('totalSales'); 
    }
    
    /**
     * @service sub_aff_sale read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service sub_aff_sale export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
