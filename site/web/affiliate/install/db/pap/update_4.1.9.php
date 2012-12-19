<?php
/**
 * Update step will delete plugin configuration file in every account 
 *
 */
class pap_update_4_1_9 {
    public function execute() {
        $this->addTemplate(new Pap_Mail_OnAffiliateJoinToCampaign());
        $this->addTemplate(new Pap_Mail_OnMerchantApproveAffiliateToCampaign());
        $this->addTemplate(new Pap_Mail_OnMerchantDeclineAffiliateForCampaign());
    }
    
    private function addTemplate(Gpf_Mail_Template $mailTemplate) {
    	$mailTemplate->setup(Gpf_Session::getAuthUser()->getAccountId());
    }
}
?>
