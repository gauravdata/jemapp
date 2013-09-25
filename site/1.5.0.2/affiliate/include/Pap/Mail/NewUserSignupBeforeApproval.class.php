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
class Pap_Mail_NewUserSignupBeforeApproval extends Pap_Mail_UserMail {
    
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'new_user_signup_before_approval.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Affiliate - New User Signup Before Approval');
        $this->subject = '{$firstname}' . Gpf_Lang::_runtime(', welcome to our affiliate program');
    }
    
    protected function initTemplateVariables() {
        parent::initTemplateVariables();
        $this->addVariable('confirmationLink', $this->_("Approve link"));
    }
    
    protected function setVariableValues() {
        parent::setVariableValues();
        $this->setVariable('confirmationLink', $this->createApproveDeclineTask(Pap_Common_Constants::STATUS_APPROVED));
    }
    
    private function createApproveDeclineTask($newStatus) {               
        $actionRequest = new Gpf_Rpc_ActionRequest('Pap_Merchants_User_AffiliateForm', 'changeStatus');
        $actionRequest->addParam('dontSendNotification', 'N');
        $actionRequest->addParam('status', $newStatus);
        $actionRequest->addParam('ids', new Gpf_Rpc_Array(array($this->getUser()->getId())));   
        
        $quickTask = new Gpf_Tasks_QuickTask($actionRequest);
        return $quickTask->getUrl();
    }
}
