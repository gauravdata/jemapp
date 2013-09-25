<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_CampaignsCategories_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'CampaignsCategories';
        $this->name = $this->_('Campaigns categories');
        $this->description = $this->_('Campaigns categories allow you to categorize your campaigns to tree-like structure. <br/><a href="%s" target="_blank">More help in our Knowledge Base</a>',Gpf_Application::getKnowledgeHelpUrl('features-and-functions/campaigns-categories.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addImplementation('PostAffiliate.merchant.menu',
                                 'Pap_Features_CampaignsCategories_Main', 'addToMenu');
        $this->addImplementation('CampaignGrid.modifyWhere',
                                 'Pap_Features_CampaignsCategories_Main', 'addCategoryFilterToMerchantCategoryList');
    }
    
    public function onDeactivate() {
    }
    
    public function onActivate() {
    }
}
?>
