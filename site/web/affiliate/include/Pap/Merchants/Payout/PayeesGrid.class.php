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
class Pap_Merchants_Payout_PayeesGrid extends Pap_Merchants_Payout_PayoutGridBase {	
    
    protected function initViewColumns() {
    	$this->addViewColumn(Pap_Db_Table_Payouts::ID, $this->_("Payout Id"), true); 
        $this->addViewColumn(Pap_Db_Table_Payouts::USER_ID, $this->_("User Id"), true); 
    	$this->addViewColumn("username", $this->_("Username"), false); 
    	$this->addViewColumn("firstname", $this->_("First name"), false);
    	$this->addViewColumn("lastname", $this->_("Last name"), false);
        $this->addViewColumn("name", $this->_("Name"), false); 
        $this->addUserAdditionalViewColumns();
        $this->addViewColumn("currency", $this->_("Currency"), false); 
        $this->addViewColumn(Pap_Db_Table_Payouts::AMOUNT, $this->_("Amount"), true); 
        $this->addViewColumn(Pap_Db_Table_Payouts::AFFILIATE_NOTE, $this->_("Note to affiliate"), true); 
        $this->addViewColumn("payoutmethod", $this->_("Payout method"), true);
        $this->addViewColumn(Pap_Db_Table_Payouts::INVOICENUMBER, $this->_("Invoice number"), true);
        
        $invoicing = Gpf_Settings::get(Pap_Settings::GENERATE_INVOICES);
        if($invoicing == Gpf::YES) {
        	$this->addViewColumn(self::ACTIONS, $this->_("Actions"), false); 
        }        
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_Payouts::ID);
        $this->addDataColumn(Pap_Db_Table_Payouts::ID);
        $this->addDataColumn(Pap_Db_Table_Payouts::USER_ID, "p.userid");
        $this->addDataColumn("username");
        $this->addDataColumn("firstname");
        $this->addDataColumn("lastname");
        $this->addUserAditionalDataColumns();
        $this->addDataColumn("symbol");
        $this->addDataColumn("wheredisplay");
        $this->addDataColumn(Pap_Db_Table_Payouts::PAYOUT_HISTORY_ID);
        $this->addDataColumn(Pap_Db_Table_Payouts::AFFILIATE_NOTE);
        $this->addDataColumn(Pap_Db_Table_Payouts::AMOUNT);
        $this->addDataColumn(Pap_Db_Table_Payouts::CURRENCY_ID, "p.currencyid");
        $this->addDataColumn(Pap_Db_Table_Payouts::INVOICE);
        $this->addDataColumn("payoutmethod", "po.".Gpf_Db_Table_FieldGroups::NAME);
        $this->addDataColumn(Pap_Db_Table_Payouts::INVOICENUMBER);
    }
    
    protected function initDefaultView() {
    	$this->addDefaultViewColumn("userid", '', 'A'); 
    	$this->addDefaultViewColumn("name", '', 'A');
    	$this->addDefaultViewColumn("firstname", '', 'A');
    	$this->addDefaultViewColumn("lastname", '', 'A');
    	$this->addDefaultViewColumn("amount", '', 'A');
        $this->addDefaultViewColumn("payoutmethod", '', 'A');
        $this->addDefaultViewColumn("invoicenumber", '', 'A');
        
        $invoicing = Gpf_Settings::get(Pap_Settings::GENERATE_INVOICES);
        if($invoicing == Gpf::YES) {
        	$this->addDefaultViewColumn(self::ACTIONS, '', 'N'); 
        }        
    }

    function buildFrom() {       
        $this->_selectBuilder->from->add(Pap_Db_Table_Payouts::getName(), "p"); 
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "p.userid = pu.userid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_FieldGroups::getName(), "po", "pu.payoutoptionid = po.fieldgroupid");
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_Currencies::getName(), "c", "p.currencyid = c.currencyid");
    }
    
    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add('p.'.Pap_Db_Table_Payouts::PAYOUT_HISTORY_ID, '=', $this->getPayoutHistoryId());
    }
    
    private function getPayoutHistoryId() {
        if ($this->_params->exists('payouthistoryid')) {
            return $this->_params->get('payouthistoryid');
        }
        throw new Gpf_Exception('PayoutHistoryID can not be empty');
    }
    
    protected function initRequiredColumns() {
        parent::initRequiredColumns();
        $this->addRequiredColumn('firstname');
        $this->addRequiredColumn('lastname');
    }
    
    /**
     * @service pay_affiliate read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service pay_affiliate export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
