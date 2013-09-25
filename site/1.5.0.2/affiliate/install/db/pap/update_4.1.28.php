<?php
/**
 *
 */
class pap_update_4_1_28 {
    public function execute() {
        $this->addTemplate(new Pap_Mail_AffiliateChangeCommissionStatus());
    }
    
    private function addTemplate(Gpf_Mail_Template $mailTemplate) {
        $mailTemplate->setup(Gpf_Session::getAuthUser()->getAccountId());
    }
}
?>
