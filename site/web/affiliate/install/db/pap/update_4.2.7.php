<?php

class pap_update_4_2_7 {
    public function execute() {
        $template = new Pap_Mail_PayDayReminder_PayDayReminder();
        $template->setup(Gpf_Session::getAuthUser()->getAccountId());
    }   
}
?>
