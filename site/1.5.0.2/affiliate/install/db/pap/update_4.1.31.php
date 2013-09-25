<?php
class pap_update_4_1_31 {
    public function execute() {
        $inviteTocampaignMail = new Pap_Mail_InviteToCampaign();
        $inviteTocampaignMail->setup(Gpf_Session::getAuthUser()->getAccountId());
    }    
}
?>
