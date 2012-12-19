<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Pap_Mail_MerchantNewDirectLinkNotification extends Pap_Mail_UserMail {
    /**
     * @var Pap_Db_DirectLinkUrl
     */
    private $directLink;
    private $quickTaskApproveUrl;
    private $quickTaskDeclineUrl;

    /**
     * @param $directLink can be null only in update step and create account task
     */
    public function __construct(Pap_Db_DirectLinkUrl $directLink = null) {
        parent::__construct();
        $this->mailTemplateFile = 'merchant_new_direct_link_notification.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Merchant - New DirectLink Notification');
        $this->subject = Gpf_Lang::_runtime('New DirectLink notification');
        $this->directLink = $directLink;
    }

    protected function initTemplateVariables() {
        $this->addVariable('directlink_url', $this->_("DirectLink URL"));
        $this->addVariable('directlink_note', $this->_("DirectLink Note"));
        $this->addVariable('directlink_approve', $this->_("DirectLink Approve Link"));
        $this->addVariable('directlink_decline', $this->_("DirectLink Decline Link"));
        parent::initTemplateVariables();
    }

    protected function setVariableValues() {
        parent::setVariableValues();
        
        $this->createQuickTaskVariables();
        $this->setVariable('directlink_url', $this->directLink->getUrl());
        $this->setVariable('directlink_note', $this->directLink->getNote());
        $this->setVariable('directlink_approve', $this->quickTaskApproveUrl);
        $this->setVariable('directlink_decline', $this->quickTaskDeclineUrl);
    }

    /**
     *
     * @param $paramName
     * @param $paramValue
     * @return string
     */
    private function createApproveDeclineTask(Gpf_Tasks_QuickTaskGroup $quickTaskGroup, $newStatus) {
        $actionRequest = new Gpf_Rpc_ActionRequest('Pap_Merchants_User_DirectLinksForm', 'quickTaskApprove');
        $actionRequest->addParam('directLinkId', $this->directLink->getId());
        $actionRequest->addParam('rstatus', $newStatus);
        $quickTask = new Gpf_Tasks_QuickTask($actionRequest);
        $quickTaskGroup->add($quickTask);
        return $quickTask->getUrl();
    }

    private function createQuickTaskVariables() {
        $quickTaskGroup = new Gpf_Tasks_QuickTaskGroup();
        $this->quickTaskApproveUrl = $this->createApproveDeclineTask($quickTaskGroup, Pap_Common_Constants::STATUS_APPROVED);
        $this->quickTaskDeclineUrl = $this->createApproveDeclineTask($quickTaskGroup, Pap_Common_Constants::STATUS_DECLINED);
    }
}
