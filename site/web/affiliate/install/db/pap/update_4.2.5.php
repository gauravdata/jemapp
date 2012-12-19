<?php

class pap_update_4_2_5 {
    public function execute() {
        $this->addTemplate(new Pap_Mail_MerchantInvoice());
        $this->addTemplate(new Pap_Mail_AffiliateInvoice());        
    }
    
    private function addTemplate(Gpf_Mail_Template $mailTemplate) {
    	$mailTemplate->setup(Gpf_Session::getAuthUser()->getAccountId());
    }
}
?>
