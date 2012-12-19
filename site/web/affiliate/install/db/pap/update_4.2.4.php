<?php

class pap_update_4_2_4 {
    public function execute() {
        $mail = new Gpf_Db_Mail();
        $mailAccount = $mail->getMailAccount();

		Gpf_Settings::set(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL, $mailAccount->getAccountEmail());
    }
}
?>
