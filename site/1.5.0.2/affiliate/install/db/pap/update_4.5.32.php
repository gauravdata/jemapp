<?php
class pap_update_4_5_32 {
    public function execute() {
        if(Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive('AffiliateNetwork')){
            $Merchant = new Gpf_Db_Role();
            $Merchant->setId(Pap_Application::DEFAULT_ROLE_MERCHANT);
            $Merchant->load();
            $Merchant->setName('Network Owner');
            $Merchant->save();
        }
    }
}

?>
