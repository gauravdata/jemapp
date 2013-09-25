<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_AffiliateTrackingCode_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'AffiliateTrackingCode';
        $this->name = $this->_('Affiliate Sale Tracking Codes');
        $this->description = $this->_('This feature allows your affiliate to place their own sale tracking codes on your thank you page. %s<br/>', '<a href="'.Gpf_Application::getKnowledgeHelpUrl('features-and-functions/affiliate-sale-tracking-codes.html').'" target="_blank">'.$this->_('More help in our Knowledge Base').'</a>');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.merchant.menu', 'Pap_Features_AffiliateTrackingCode_Main', 'addToMerchantMenu');
        $this->addImplementation('Tracker.action.afterSaveCommissions', 'Pap_Features_AffiliateTrackingCode_Main', 'displayAffiliateTrackingCode');

        $this->addImplementation('PostAffiliate.Affiliates.Campaigns.initViewColumns', 'Pap_Features_AffiliateTrackingCode_Main', 'initAffiliateCampaignsGridViewColumns');
        $this->addImplementation('PostAffiliate.Affiliates.Campaigns.initDefaultView', 'Pap_Features_AffiliateTrackingCode_Main', 'initAffiliateCampaignsGridDefaultView');
        $this->addImplementation('PostAffiliate.AffiliatePrivileges.initDefault', 'Pap_Features_AffiliateTrackingCode_Main', 'initDefaultAffiliatePrivileges');
    }

    public function onActivate() {
        Gpf_Settings::set(Pap_Settings::VISIT_OFFLINE_PROCESSING_DISABLE, 'Y');
    }

    public function onDeactivate() {
        Gpf_Settings::set(Pap_Settings::VISIT_OFFLINE_PROCESSING_DISABLE, '');
    }
}
?>
