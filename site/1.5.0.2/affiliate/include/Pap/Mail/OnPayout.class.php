<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Pap_Mail_OnPayout extends Pap_Mail_UserMail {

    /**
     * @var Pap_Common_Payout
     */
	protected $payout;
	
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'on_payout.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Affiliate - On payout');
        $this->subject = Gpf_Lang::_runtime('Your commissions');
    }
    
    protected function initTemplateVariables() {
    	parent::initTemplateVariables();
    	
        $this->addVariable('payment', $this->_('Paid amount'));
    	$this->addVariable('payoutcurrency', $this->_('Paid currency'));
        $this->addVariable('payoutmethod', $this->_('Payout method'));
        $this->addVariable('invoicenumber', $this->_('Invoice number'));
    }

    protected function setVariableValues() {
    	parent::setVariableValues();
    	
    	$this->setVariable('payment', $this->payout->getAmountAsText());
    	$this->setVariable('payoutcurrency', $this->payout->getCurrency()->getSymbol());
    	$this->setVariable('invoicenumber', $this->payout->getInvoiceNumber());
    	if ($this->payout->getPayoutOption() != null) {
            $this->setVariable('payoutmethod', $this->_localize($this->payout->getPayoutOption()->getName()));    	    
    	}
    }

    public function setPayout(Pap_Common_Payout $payout) {
    	$this->payout = $payout;
    }
}
