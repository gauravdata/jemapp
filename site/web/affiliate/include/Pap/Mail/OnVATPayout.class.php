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
class Pap_Mail_OnVATPayout extends Pap_Mail_OnPayout {
   
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'on_vat_payout.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Affiliate - On VAT payout');
        $this->subject = Gpf_Lang::_runtime('Your commissions');
    }
    
    protected function initTemplateVariables() {
    	parent::initTemplateVariables();
    	
        $this->addVariable('vat_percentage', $this->_('VAT percentage'));
    	$this->addVariable('vat_number', $this->_('VAT number'));
        $this->addVariable('reg_number', $this->_('Registration number'));
        $this->addVariable('payment_vat_part', $this->_('VAT part'));
        $this->addVariable('payment_incl_vat', $this->_('Payment incl. VAT'));
        $this->addVariable('payment_excl_vat', $this->_('Payment excl. VAT'));
        $this->addVariable('invoicenumber',$this->_('Invoice number'));
    }

    protected function setVariableValues() {
    	parent::setVariableValues();
    	
    	$this->setVariable('vat_percentage', $this->payout->getVatPercentage());
    	$this->setVariable('vat_number', $this->payout->getVatNumber());
    	$this->setVariable('reg_number', $this->payout->getRegNumber());
    	$this->setVariable('payment_vat_part', $this->payout->getAmountVatPart());
    	$this->setVariable('payment_incl_vat', $this->payout->getAmountWithVat());
    	$this->setVariable('payment_excl_vat', $this->payout->getAmountWithoutWat());
    	$this->setVariable('invoicenumber', $this->payout->getInvoiceNumber());
    }    
}
