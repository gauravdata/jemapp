<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
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

class Pap_Api_PayoutsGrid extends Gpf_Rpc_GridRequest {
    
    private $affiliatesToPay = array();
    
    public function __construct(Gpf_Api_Session $session) {
        if($session->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            throw new Gpf_Exception('Only merchant can view payouts grid. Please login as merchant.');
        }
        
        $className = 'Pap_Merchants_Payout_PayAffiliatesGrid';
        parent::__construct($className, 'getRows', $session);
    }
    
    public function payAffiliates($paymentNote = '', $affiliateNote = '', $send_payment_to_affiliate = Gpf::NO, $send_generated_invoices_to_merchant = Gpf::NO, $send_generated_invoices_to_affiliates = Gpf::NO) {
        $this->checkMerchantRole();
        if (count($this->getAffiliatesToPay()) == 0) {
            throw new Gpf_Exception('You must select at least one affiliate to pay.');
        }
        try {
            $this->sendMarkTransactionsCall();
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception('Error during marking as pending payments: ' . $e->getMessage());
        }
        try {
           $this->sendPayTransactionsCall($paymentNote, $affiliateNote, $send_payment_to_affiliate, $send_generated_invoices_to_merchant, $send_generated_invoices_to_affiliates);
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception('Error during paying affiliates: ' . $e->getMessage());
        }
    }
    
    protected function sendMarkTransactionsCall() {
        $request = new Gpf_Rpc_ActionRequest('Pap_Merchants_Payout_PayAffiliatesFormExportGrid', 'markTransactionsAsPaymentPending', $this->apiSessionObject);
        $request->addParam('ids', new Gpf_Rpc_Array($this->getAffiliatesToPay()));
        $request->addParam('filters', new Gpf_Rpc_Array($this->getFilters()));
        $request->sendNow();
        
        if ($request->getResponseError() != '') {
            throw new Gpf_Exception($request->getResponseError());
        }
    }
    
    protected function sendPayTransactionsCall($paymentNote, $affiliateNote, $send_payment_to_affiliate, $send_generated_invoices_to_merchant, $send_generated_invoices_to_affiliates) {
        $request = new Gpf_Rpc_FormRequest('Pap_Merchants_Payout_PayAffiliatesForm', 'payAffiliates', $this->apiSessionObject);
        $request->setField('paymentNote', $paymentNote);
        $request->setField('affiliateNote', $affiliateNote);
        $request->setField('send_payment_to_affiliate', $send_payment_to_affiliate);
        $request->setField('send_generated_invoices_to_merchant', $send_generated_invoices_to_merchant);
        $request->setField('send_generated_invoices_to_affiliates', $send_generated_invoices_to_affiliates);
        $request->sendNow();
       
        if ($request->getResponseError() != '') {
            throw new Gpf_Exception($request->getResponseError());
        }
    }
    
    public function addAllAffiliatesToPay() {
        $this->checkMerchantRole();
        try {
            $grid = $this->getGrid();
            $recordset = $grid->getRecordset();
            foreach($recordset as $rec) {
                $this->addAffiliateToPay($rec->get('id'));
            }
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception('You must load list of affiliates first!');
        }
    }
    
    public function addAffiliateToPay($affiliateId) {
        if(!in_array($affiliateId, $this->affiliatesToPay)) {
            $this->affiliatesToPay[] = $affiliateId;
        }
    }
    
    public function getAffiliatesToPay() {
        return $this->affiliatesToPay;
    }
    
    private function checkMerchantRole() {
        if($this->apiSessionObject->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            throw new Gpf_Exception('Only merchant is allowed to pay affiliates.');
        }
    }
}
?>
