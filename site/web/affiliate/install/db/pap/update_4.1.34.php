<?php
/**
 * Update step will delete plugin configuration file in every account
 *
 */
class pap_update_4_1_34 {
    public function execute() {
        $task  = new Pap_Install_CreateAccountTask();
        $task->setupDefaultBannerWrappers();

        $row = new Pap_Db_BannerWrapper();
        $row->setId('script');
        $row->setName('##Script##');
        $row->setCode('<script type="text/javascript" src="{$'.Pap_Merchants_Config_BannerWrapperService::CONST_HTMLJSURL.'}"></script>');
        $row->save();

        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_Banners::getName());
        $update->set->add(Pap_Db_Table_Banners::WRAPPER_ID, 'plain');
        $update->execute();
        
        $this->updateWrapper(
        array(Pap_Common_Banner_Factory::BannerTypeHtml), 
        Pap_Db_Table_Banners::DATA1);

        $this->updateWrapper(
        array(Pap_Common_Banner_Factory::BannerTypeImage, Pap_Common_Banner_Factory::BannerTypeText), 
        Pap_Db_Table_Banners::DATA3);
        
        $this->updateWrapper(
        array(Pap_Common_Banner_Factory::BannerTypeFlash, Pap_Features_HoverBanner_Hover::TYPE_HOVER), 
        Pap_Db_Table_Banners::DATA4);
    }
    
    private function updateWrapper($bannerTypes, $dataField) {   
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_Banners::getName());
        $update->set->add(Pap_Db_Table_Banners::WRAPPER_ID, 'script');
        $update->where->add(Pap_Db_Table_Banners::TYPE, 'IN', $bannerTypes);
        $update->where->add($dataField, '=', Gpf::YES);
        $update->execute();
    }
}
?>
