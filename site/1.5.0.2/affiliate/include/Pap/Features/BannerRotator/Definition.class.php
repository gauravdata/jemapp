<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_BannerRotator_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'BannerRotator';
        $this->name = $this->_('Banner Rotator');
        $this->description = $this->_('Banner rotator is a special type of banner that rotates other banners.<br/>When affiliate inserts rotator to his page, the rotator displays banners that are assigned to the rotator, so the visitor will see another banner every time he visits the affiliates page.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>', Gpf_Application::getKnowledgeHelpUrl('features-and-functions/banner-rotator.html'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addImplementation('PostAffiliate.BannerFactory.getBannerObjectFromType', 'Pap_Features_BannerRotator_Config', 'getBanner');
        
        $this->addImplementation('Tracker.impression.afterSave', 'Pap_Features_BannerRotator_Tracking', 'saveRotatorImpression');

        $this->addImplementation('Tracker.click.beforeSaveClick', 'Pap_Features_BannerRotator_Tracking', 'saveRotatorClick');
        
        $this->addImplementation('PostAffiliate.Pap_Db_Table_ClicksImpressions.getStatsSelect', 'Pap_Features_BannerRotator_Main', 'getStatsSelect');

        $this->addImplementation('Tracker.ImpressionProcessor.getAllImpressions', 'Pap_Features_BannerRotator_Tracking', 'getAllImpressionsForProcessing');
    }
    
    public function onDeactivate() {
    	$delete = new Gpf_SqlBuilder_DeleteBuilder();
    	$delete->from->add(Pap_Db_Table_Banners::getName());
    	$delete->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_BannerRotator_Config::BannerTypeRotator);
    	$delete->execute();
    }
}
?>
