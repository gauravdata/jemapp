<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Pap_Mail_PayDayReminder_PayDayReminder extends Gpf_Mail_Template {

	/**
	 * @var Pap_Mail_PayDayReminder_PayAffiliatesList
	 */
	private $payAffiliatesList;
	
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'pay_day_reminder.stpl';
        $this->templateName = Gpf_Lang::_runtime('Merchant - Pay day reminder');
        $this->subject = Gpf_Lang::_runtime('Pay day reminder');
        $this->payAffiliatesList = new Pap_Mail_PayDayReminder_PayAffiliatesList();
    }
    
    protected function initTemplateVariables() {
    	$this->addVariable('currency', $this->_("Currency"));
    	$this->addVariable('amounttopay', $this->_("Amount to pay"));
    	$this->addVariable('approvedcommissions', $this->_("Approved commissions"));  
        $this->addVariable('pendingcommissions', $this->_("Pending commissions"));        
        $this->addVariable('declinedcommissions', $this->_("Declined commissions")); 
        $this->addVariable('payaffiliateslist', $this->_("Pay affiliates list"));        
    }

    protected function setVariableValues() {
    	$this->setVariable('currency', Pap_Common_Utils_CurrencyUtils::getDefaultCurrency()->getSymbol());
    	$this->setVariable('payaffiliateslist', $this->payAffiliatesList->getHtml());
    	$this->setVariable('amounttopay', $this->payAffiliatesList->getAmountToPay());
        $this->setVariable('approvedcommissions', $this->payAffiliatesList->getApprovedCommissions());
        $this->setVariable('pendingcommissions', $this->payAffiliatesList->getPendingCommissions());
        $this->setVariable('declinedcommissions', $this->payAffiliatesList->getDeclinedCommissions());        
    }    
}
