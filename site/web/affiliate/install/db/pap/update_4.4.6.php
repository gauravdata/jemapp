<?php
class pap_update_4_4_6 {

    public function execute() {
        if (in_array(Pap_Features_SplitCommissions_Definition::CODE ,Gpf_Plugins_Engine::getInstance()->getConfiguration()->getActivePlugins())) {
             $template = new Pap_Mail_SplitCommissionsMerchantOnSale();
             $template->setup(Gpf_Session::getAuthUser()->getAccountId());
        }
    }
}
?>
