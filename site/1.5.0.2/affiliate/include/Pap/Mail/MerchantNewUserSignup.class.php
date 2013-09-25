<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Pap_Mail_MerchantNewUserSignup extends Pap_Mail_UserMail {
   
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'merchant_new_user_signup.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Merchant - New User Signup');
        $this->subject = Gpf_Lang::_runtime('New user signed up');
    }

    protected function initTemplateVariables() {
        $this->addVariable('new_user_signup_status', $this->_("New User Signup Status"));
        $this->addVariable('new_user_signup_approve_link', $this->_("New User Signup Approve Link"));
        $this->addVariable('new_user_signup_decline_link', $this->_("New User Signup Decline Link"));
        parent::initTemplateVariables();
    }
   
    protected function setVariableValues() {
        parent::setVariableValues();
        $quickTaskGroup = new Gpf_Tasks_QuickTaskGroup();
        $this->setVariable('new_user_signup_approve_link', 
                            $this->createApproveDeclineTask($quickTaskGroup, Pap_Common_Constants::STATUS_APPROVED));
        $this->setVariable('new_user_signup_decline_link',
                            $this->createApproveDeclineTask($quickTaskGroup, Pap_Common_Constants::STATUS_DECLINED));
        $this->setVariable('new_user_signup_status', $this->getUser()->getStatus());
    }

    /**
     *
     * @param $paramName
     * @param $paramValue
     * @return string
     */

    private function createApproveDeclineTask(Gpf_Tasks_QuickTaskGroup $quickTaskGroup, $newStatus) {               
        $actionRequest = new Gpf_Rpc_ActionRequest('Pap_Merchants_User_AffiliateForm', 'changeStatus');
        $actionRequest->addParam('dontSendNotification', 'N');
        $actionRequest->addParam('status', $newStatus);
        $actionRequest->addParam('ids', new Gpf_Rpc_Array(array($this->getUser()->getId())));   
        
        $quickTask = new Gpf_Tasks_QuickTask($actionRequest);
        $quickTaskGroup->add($quickTask);
        return $quickTask->getUrl();
    }
}
