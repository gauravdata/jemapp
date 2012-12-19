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
class Pap_Mail_MerchantOnContactUs extends Pap_Mail_UserMail {

	private $userSubject;
	private $userText;
	
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'merchant_contact_us.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Merchant - Affiliate filled Contact us form');
        $this->subject = Gpf_Lang::_runtime('Affiliate filled Contact us form');
    }
    
    public function setEmail($subject, $text) {
    	$this->userSubject = str_replace('\n', '<br>', $subject);
    	$this->userText = str_replace('\n', '<br>', $text);
    }
    
    protected function initTemplateVariables() {
    	parent::initTemplateVariables();
        $this->addVariable("emailsubject", $this->_("Email subject"));
        $this->addVariable("emailtext", $this->_("Email text"));
    }

    protected function setVariableValues() {
    	parent::setVariableValues();
    	$this->setVariable("emailsubject", $this->userSubject);
    	$this->setVariable("emailtext", $this->userText);
    }    
}
