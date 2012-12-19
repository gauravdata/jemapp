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
class Pap_Mail_MerchantOnSale extends Pap_Mail_SaleMail {
    
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'merchant_on_sale.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Merchant - New Sale / Lead');
        $this->subject = Gpf_Lang::_runtime('New sale / lead');
    }
    
    protected function initTemplateVariables() {
        $this->addVariable('sale_approve_link', $this->_("Sale Approve Link"));
        $this->addVariable('sale_decline_link', $this->_("Sale Decline Link"));
        parent::initTemplateVariables();
    }

    protected function setVariableValues() {
        $quickTaskGroup = new Gpf_Tasks_QuickTaskGroup();
        $this->setVariable('sale_approve_link', 
                            $this->createApproveDeclineTask($quickTaskGroup, Pap_Common_Constants::STATUS_APPROVED));
        $this->setVariable('sale_decline_link',
                            $this->createApproveDeclineTask($quickTaskGroup, Pap_Common_Constants::STATUS_DECLINED));
        parent::setVariableValues();
    }   
    
    private function createApproveDeclineTask(Gpf_Tasks_QuickTaskGroup $quickTaskGroup, $newStatus) {               
        $actionRequest = new Gpf_Rpc_ActionRequest('Pap_Merchants_Transaction_TransactionsForm', 'changeStatus');
        $actionRequest->addParam('status', $newStatus);
        $actionRequest->addParam('ids', new Gpf_Rpc_Array(array($this->transaction->get(Pap_Db_Table_Transactions::TRANSACTION_ID))));   
        
        $quickTask = new Gpf_Tasks_QuickTask($actionRequest);
        $quickTaskGroup->add($quickTask);
        return $quickTask->getUrl();
    }
    
}
