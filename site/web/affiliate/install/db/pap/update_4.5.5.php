<?php
class pap_update_4_5_5 {

    public function execute() {
        if (in_array(Pap_Features_SplitCommissions_Definition::CODE ,Gpf_Plugins_Engine::getInstance()->getConfiguration()->getActivePlugins())) {
            $this->deleteSplitCommissionsMailTemplate();
            $this->createSplitCommissionsMailTemplate();
        }
    }

    private function createSplitCommissionsMailTemplate() {
        $template = new Pap_Mail_SplitCommissionsMerchantOnSale();
        $template->setup(Gpf_Session::getAuthUser()->getAccountId());
    }

    private function deleteSplitCommissionsMailTemplate() {
        $dbTemplate = new Gpf_Db_MailTemplate();
        $dbTemplate->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
        $dbTemplate->setClassName('Pap_Mail_SplitCommissionsMerchantOnSale');
        $dbTemplate->loadFromData();
        $dbTemplate->delete();
    }
}
?>
