<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_Payout extends Gpf_DbEngine_Row {    
        
    function __construct(){
        parent::__construct();      
    }
    
    function init() {
        $this->setTable(Pap_Db_Table_Payouts::getInstance());
        parent::init();
    }
    
    public function getInvoice() {
    	return $this->get(Pap_Db_Table_Payouts::INVOICE);
    }

    public function setUserId($id) {
    	$this->set(Pap_Db_Table_Payouts::USER_ID, $id);
    }
    
    public function getUserId() {
        return $this->get(Pap_Db_Table_Payouts::USER_ID);
    }
    
    public function setPayoutHistoryId($id) {
        $this->set(Pap_Db_Table_Payouts::PAYOUT_HISTORY_ID, $id);
    }
    
    public function getPayoutHistoryId() {
        return $this->get(Pap_Db_Table_Payouts::PAYOUT_HISTORY_ID);
    }
    
    public function setAffiliateNote($note) {
        $this->set(Pap_Db_Table_Payouts::AFFILIATE_NOTE, $note);
    }
    
    public function getAffiliateNote() {
        return $this->get(Pap_Db_Table_Payouts::AFFILIATE_NOTE);
    }
    
    public function setAmount($amount) {
        $this->set(Pap_Db_Table_Payouts::AMOUNT, $amount);
    }
    
    public function getAmount() {
        return $this->get(Pap_Db_Table_Payouts::AMOUNT);
    }
    
    public function getAmountAsText() {
    	return round($this->getAmount(), Pap_Common_Utils_CurrencyUtils::getDefaultCurrencyPrecision());
    }
    
    public function getInvoiceNumber() {
    	return $this->get(Pap_Db_Table_Payouts::INVOICENUMBER);
    }
    
    public function setInvoiceNumber($invoiceNumber) {
        return $this->set(Pap_Db_Table_Payouts::INVOICENUMBER, $invoiceNumber);
    }
    
    public function setInvoice($invoice) {
        return $this->set(Pap_Db_Table_Payouts::INVOICE, $invoice);
    }
    
    
}

?>
