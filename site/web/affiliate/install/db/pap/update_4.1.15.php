<?php

class pap_update_4_1_15 {
    public function execute() {
        $this->addTemplate(new Pap_Mail_AffiliateDirectLinkNotification());
        $this->addTemplate(new Pap_Mail_MerchantNewDirectLinkNotification());
    }
    
    private function addTemplate(Gpf_Mail_Template $mailTemplate) {
    	$mailTemplate->setup(Gpf_Session::getAuthUser()->getAccountId());
    }
}
?>
