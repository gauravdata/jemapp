<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package ShopMachine
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
class Pap_Mail_MerchantInvoice extends Pap_Mail_UserMail {
	
	/**
     * @var Pap_Common_Payout
     */
	protected $payout;
	
    public function __construct() {
        parent::__construct();        
        $this->isHtmlMail = true;        
        $this->subject = Gpf_Lang::_runtime('Invoice');
        $this->init();
    }    
    
    protected function init() {
    	$this->mailTemplateFile = 'merchant_invoice.stpl';
    	$this->templateName = Gpf_Lang::_runtime('Merchant - Invoice');
    }
    
    protected function initTemplateVariables() {
    	parent::initTemplateVariables();    	
        $this->addVariable('invoice', $this->_('Invoice'));
        $this->addVariable('affiliatenote', $this->_('Affiliate note'));
        $this->addVariable('amount', $this->_('Amount'));
        $this->addVariable('invoicenumber', $this->_('Invoice number'));
    }

    protected function setVariableValues() {
    	parent::setVariableValues();    	
    	$this->setVariable('invoice', $this->payout->getInvoice());
    	$this->setVariable('affiliatenote', $this->payout->getAffiliateNote());
    	$this->setVariable('amount', $this->payout->getAmountAsText());
    	$this->setVariable('invoicenumber', $this->payout->getInvoiceNumber());
    }

    public function setPayout(Pap_Common_Payout $payout) {
    	$this->payout = $payout;
    }
}
