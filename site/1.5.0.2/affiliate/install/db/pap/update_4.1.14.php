<?php
/**
 * Update step will delete plugin configuration file in every account 
 *
 */
class pap_update_4_1_14 {
    public function execute() {
        $mailTemplate = new Pap_Mail_NewUserSignupDeclined();
        $mailTemplate->setup(Gpf_Session::getAuthUser()->getAccountId());
    }
}
?>
