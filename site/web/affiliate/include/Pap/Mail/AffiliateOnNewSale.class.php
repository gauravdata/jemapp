<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
 * @package PostAffiliatePro
 */
class Pap_Mail_AffiliateOnNewSale extends Pap_Mail_SaleMail {
    
    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'affiliate_on_new_sale.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Affiliate - New Sale / Lead');
        $this->subject = Gpf_Lang::_runtime('New sale / lead');
    }
    
    
    
    public function updateTransactionFields($transactionFields) {
        $transaction = $this->transaction;
        
        if($transaction->getAllowFirstClickData() == Gpf::NO) {
            $transaction->setFirstClickTime($this->getForbiddenClickRefererText());
            $transaction->setFirstClickReferer($this->getForbiddenClickRefererText());
            $transaction->setFirstClickIp($this->getForbiddenClickRefererText());
            $transaction->setFirstClickData1($this->getForbiddenClickRefererText());
            $transaction->setFirstClickData2($this->getForbiddenClickRefererText());
        }
        
        if($transaction->getAllowLastClickData() == Gpf::NO) {
            $transaction->setLastClickTime($this->getForbiddenClickRefererText());
            $transaction->setLastClickReferer($this->getForbiddenClickRefererText());
            $transaction->setLastClickIp($this->getForbiddenClickRefererText());
            $transaction->setLastClickData1($this->getForbiddenClickRefererText());
            $transaction->setLastClickData2($this->getForbiddenClickRefererText());
        }
        $transactionFields->removeFromCache($transaction->getId());
        $transactionFields->setTransaction($transaction);
    }
    
    public function getForbiddenClickRefererText(){
        return $this->_("Other affiliate (hidden due privacy)");
    }

    
}