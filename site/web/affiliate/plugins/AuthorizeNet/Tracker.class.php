<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Juraj Simon
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
 * @package PostAffiliatePro plugins
 */
class AuthorizeNet_Tracker extends Pap_Tracking_CallbackTracker {
	
	private $subscriptionPaynum;
	private $invoiceNumber;
	
	protected function getSubscriptionPaynum() {
		return $this->subscriptionPaynum;
	}
	
	protected function setSubscriptionPaynum($paynum) {
		$this->subscriptionPaynum = $paynum;
	}
	
	protected function getInvoiceNumber() {
		return $this->invoiceNumber;
	}
	
	protected function setInvoiceNumber($invoicenumber) {
		$this->invoiceNumber = $invoicenumber;
	}
	
    protected function discountFromTotalcost ($totalcost, $value) {
        if (($value != '') && (is_numeric($value))) {
            return $totalcost - $value;
        }
        return $totalcost;
    }
	
	private function adjustTotalCost($originalTotalCost, Pap_Tracking_Request $request) {
		$totalCost = $originalTotalCost;
        $this->debug('Original totalcost: '.$totalCost);
        
	    if (Gpf_Settings::get(AuthorizeNet_Config::DISCOUNT_TAX)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('x_tax'));
            $this->debug('Discounting tax ('.$request->getPostParam('x_tax').') from totalcost.');
        }
	    if (Gpf_Settings::get(AuthorizeNet_Config::DUTY_TAX)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('x_duty'));
            $this->debug('Discounting duty tax ('.$request->getPostParam('x_duty').') from totalcost.');
        }
	    if (Gpf_Settings::get(AuthorizeNet_Config::FREIGHT_TAX)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('x_freight'));
            $this->debug('Discounting freight tax ('.$request->getPostParam('x_freight').') from totalcost.');
        }
        $this->debug('Totalcost after discounts: '.$totalCost);
        return $totalCost;
	}
	
    /**
     *
     * @return Pap_Common_Transaction
     */
    protected function getParentTransaction($subscriptionId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
         
        $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::DATA5, "=", $subscriptionId);

        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "IN", array(Pap_Common_Constants::TYPE_SALE, Pap_Common_Constants::TYPE_ACTION, Pap_Common_Constants::TYPE_LEAD));
        $select->where->add(Pap_Db_Table_Transactions::TIER, "=", "1");
         
        $select->limit->set(0, 1);
        $t = new Pap_Common_Transaction();
        $t->fillFromRecord($select->getOneRow());

        return $t;
    }

    protected function getTransactionObject($subscriptionId) {
    	//we matching for invoice number not subscription id!
        return $this->getParentTransaction($this->getInvoiceNumber());
    }
	
    /**
     * @return AuthorizeNet_Tracker
     */
    public function getInstance() {
        $tracker = new AuthorizeNet_Tracker();
        $tracker->setTrackerName("AuthorizeNet");
        return $tracker;
    }
    
    public function checkStatus() {
        $code = $this->getPaymentStatus();
        
        if ($code != 1) {
            $this->debug('Transaction failed');
            return false;
        }
        
        return true;
    }
    
    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }

    public function readRequestVariables() {
        $request = $this->getRequestObject();
        
        $cookieValue = $request->getPostParam(Gpf_Settings::get(AuthorizeNet_Config::PARAM_NAME));
        $descValue = $request->getPostParam('x_description');
        
        $this->setProductID($descValue);
        $this->setCookie($cookieValue);
        
        $this->setTotalCost($this->adjustTotalCost($request->getPostParam('x_amount'), $request));
        $this->setEmail($request->getPostParam('x_email'));
        $this->setTransactionID($request->getPostParam('x_trans_id'));
        $this->setPaymentStatus($request->getPostParam('x_response_code'));                
        
        $this->setSubscriptionId(@$request->getPostParam('x_subscription_id'));        
        $this->setSubscriptionPaynum(@$request->getPostParam('x_subscription_paynum'));
        $this->setData4($request->getPostParam('x_invoice_num'));               
        if ($this->getSubscriptionId()!='' && $this->getSubscriptionPaynum()!='') {
        	$this->debug('Recurring payment, saving invoice number ('.$request->getPostParam('x_invoice_num').') to data 4, and recurring number ('.$request->getPostParam('x_subscription_paynum').') into data 3.');        	
        	$this->setData3($this->getSubscriptionPaynum());        	        	
        	$this->setInvoiceNumber($this->getData4());
        } else {
        	$this->debug('New payment saving invoice number ('.$request->getPostParam('x_invoice_num').') to data5.');     	
        	$this->setData5($this->getData4());
        }
    }
    
    public function isRecurring() {
        if ($this->getSubscriptionId()!='' && $this->getSubscriptionPaynum()!='') {
        	return true;
        }
        return false;
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }
}
?>
