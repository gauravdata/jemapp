<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_PrivateCampaigns_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'PrivateCampaigns';
        $this->name = $this->_('Private campaigns');
        $this->description = $this->_('By default, all campaigns in %s are public, which means that all affiliates can view and promote them. Private campaigns feature allows you to create private campaigns, which will be visible only to selected affiliates.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO), Gpf_Application::getKnowledgeHelpUrl('features-and-functions/private-campaigns.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.CampaignsGrid.initViewColumns', 'Pap_Features_PrivateCampaigns_Main', 'initViewColumns');
        $this->addImplementation('PostAffiliate.CampaignsGrid.initDefaultView', 'Pap_Features_PrivateCampaigns_Main', 'initDefaultView');
        $this->addImplementation('PostAffiliate.CampaignsGrid.afterExecute', 'Pap_Features_PrivateCampaigns_Main', 'setAffStatus');
        $this->addImplementation('PostAffiliate.BannersGrid.buildWhere', 'Pap_Features_PrivateCampaigns_Main', 'filterBanners');
        $this->addImplementation('PostAffiliate.Campaigns.getCampaignsForAffiliate', 'Pap_Features_PrivateCampaigns_Main', 'getCampaigns');
        $this->addImplementation('PostAffiliate.RecognizeCommGroup.getCommissionGroup', 'Pap_Features_PrivateCampaigns_Main', 'getCommissionGroup');
        $this->addImplementation('PostAffiliate.BannerListbox.getBannerSelect', 'Pap_Features_PrivateCampaigns_Main', 'getBannerSelect');
        $this->addImplementation('PostAffiliate.Campaigns.getCampaignsSelect', 'Pap_Features_PrivateCampaigns_Main', 'getCampaignsSelect');
        $this->addImplementation('PostAffiliate.UserInCommissionGroup.addUser', 'Pap_Features_PrivateCampaigns_Main', 'sendNotificationToMerchant');
        $this->addImplementation('PostAffiliate.UserInCommissionGroup.changeStatus', 'Pap_Features_PrivateCampaigns_Main', 'sendNotificationToAffiliate');
        $this->addImplementation('PostAffiliate.AffiliatesGrid.createResultSelect', 'Pap_Features_PrivateCampaigns_Main', 'createResultSelectAffGrid');
        $this->addImplementation('PostAffiliate.MassMailAffiliatesGrid.addFilter', 'Pap_Features_PrivateCampaigns_Main', 'processMassMailFilter');
    }

    public function onDeactivate() {
        $this->processCampaignsToPublic();
    }

    private function processCampaignsToPublic() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_Campaigns::getName());
        $update->set->add(Pap_Db_Table_Campaigns::TYPE, Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC);
        $update->execute();
    }
}
?>
