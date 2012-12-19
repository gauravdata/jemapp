<?php
class pap_update_4_5_39 {
    public function execute() {
        $mailTemplate = new Pap_Mail_MerchantOnCommissionApproved();
        $mailTemplate->setup(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
    }
}

?>
