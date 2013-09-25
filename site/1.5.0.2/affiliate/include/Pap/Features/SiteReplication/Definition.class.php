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

class Pap_Features_SiteReplication_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'SiteReplication';
        $this->name = $this->_('Site Replication');
        $this->description = $this->_('Site Replication is a special type of banner that replicates pages created by you so they are specific for affiliate. They can contain variables like affiliate refid, name, ... .%s<br/>', '<a href="'.Gpf_Application::getKnowledgeHelpUrl('features-and-functions/site-replication.html').'" target="_blank">More help in our Knowledge Base</a>');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addImplementation('PostAffiliate.BannerFactory.getBannerObjectFromType', 'Pap_Features_SiteReplication_Config', 'getBanner');
        $this->addImplementation('PostAffiliate.BannerForm.load', 'Pap_Features_SiteReplication_Config', 'load');
    }
    
    public function onDeactivate() {
    	$delete = new Gpf_SqlBuilder_DeleteBuilder();
    	$delete->from->add(Pap_Db_Table_Banners::getName());
    	$delete->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_SiteReplication_Config::BannerTypeSite);
    	$delete->execute();
    }
}
?>
