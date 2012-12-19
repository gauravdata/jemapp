<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CommissionsExport.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Merchants_Campaign_CommissionsImportExport extends Gpf_Csv_ObjectImportExport {
	
    public function __construct() {
    	parent::__construct();
        $this->setName(Gpf_Lang::_runtime('Commissions'));
        $this->setDescription(Gpf_Lang::_runtime("CommissionsImportExportDescription"));
    }
    
    protected function writeData() {
        $this->writeSelectBuilder($this->getCommissions());
    }
    
    protected function deleteData() {
    	$deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add(Pap_Db_Table_Transactions::getName());
        $deleteBuilder->execute();
    }
    
    protected function readData() {
    	$this->readDbRow('Pap_Common_Transaction', $this->getArrayHeaderColumns($this->getCommissions()));
    }
    
    protected function checkData() {
        $this->checkFile($this->getArrayHeaderColumns($this->getCommissions()));
        $this->rewindFile();
    }
    
    protected function beforeSave(Gpf_DbEngine_Row $dbRow) {    
        $dbRow->setNotification(false);    
    }
    
    private function getCommissions() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Transactions::TRANSACTION_ID, Pap_Db_Table_Transactions::TRANSACTION_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::USER_ID, Pap_Db_Table_Transactions::USER_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::BANNER_ID, Pap_Db_Table_Transactions::BANNER_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::PARRENT_BANNER_ID, Pap_Db_Table_Transactions::PARRENT_BANNER_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::CAMPAIGN_ID, Pap_Db_Table_Transactions::CAMPAIGN_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::COUNTRY_CODE, Pap_Db_Table_Transactions::COUNTRY_CODE);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::R_STATUS, Pap_Db_Table_Transactions::R_STATUS);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::R_TYPE, Pap_Db_Table_Transactions::R_TYPE);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::DATE_INSERTED, Pap_Db_Table_Transactions::DATE_INSERTED);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::DATE_APPROVED, Pap_Db_Table_Transactions::DATE_APPROVED);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::PAYOUT_STATUS, Pap_Db_Table_Transactions::PAYOUT_STATUS);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::REFERER_URL, Pap_Db_Table_Transactions::REFERER_URL);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::IP, Pap_Db_Table_Transactions::IP);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::BROWSER, Pap_Db_Table_Transactions::BROWSER);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::COMMISSION, Pap_Db_Table_Transactions::COMMISSION);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::RECURRING_COMM_ID, Pap_Db_Table_Transactions::RECURRING_COMM_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::CLICK_COUNT, Pap_Db_Table_Transactions::CLICK_COUNT);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_TIME, Pap_Db_Table_Transactions::FIRST_CLICK_TIME);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, Pap_Db_Table_Transactions::FIRST_CLICK_REFERER);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_IP, Pap_Db_Table_Transactions::FIRST_CLICK_IP);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, Pap_Db_Table_Transactions::FIRST_CLICK_DATA1);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, Pap_Db_Table_Transactions::FIRST_CLICK_DATA2);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_TIME, Pap_Db_Table_Transactions::LAST_CLICK_TIME);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_REFERER, Pap_Db_Table_Transactions::LAST_CLICK_REFERER);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_IP, Pap_Db_Table_Transactions::LAST_CLICK_IP);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_DATA1, Pap_Db_Table_Transactions::LAST_CLICK_DATA1);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::LAST_CLICK_DATA2, Pap_Db_Table_Transactions::LAST_CLICK_DATA2);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::TRACK_METHOD, Pap_Db_Table_Transactions::TRACK_METHOD);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::ORDER_ID, Pap_Db_Table_Transactions::ORDER_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::PRODUCT_ID, Pap_Db_Table_Transactions::PRODUCT_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::TOTAL_COST, Pap_Db_Table_Transactions::TOTAL_COST);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::DATA1, Pap_Db_Table_Transactions::DATA1);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::DATA2, Pap_Db_Table_Transactions::DATA2);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::DATA3, Pap_Db_Table_Transactions::DATA3);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::DATA4, Pap_Db_Table_Transactions::DATA4);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::DATA5, Pap_Db_Table_Transactions::DATA5);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::SYSTEMNOTE, Pap_Db_Table_Transactions::SYSTEMNOTE);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::MERCHANTNOTE, Pap_Db_Table_Transactions::MERCHANTNOTE);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID, Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE, Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::TIER, Pap_Db_Table_Transactions::TIER);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, Pap_Db_Table_Transactions::PAYOUTHISTORY_ID);
        $selectBuilder->select->add(Pap_Db_Table_Transactions::CHANNEL, Pap_Db_Table_Transactions::CHANNEL);
        $selectBuilder->from->add(Pap_Db_Table_Transactions::getName());
        
        return $selectBuilder;
    }
}
?>
