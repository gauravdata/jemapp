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

class Pap_Features_ZipBanner_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'ZipBanner';
        $this->name = $this->_('Zip Banner');
        $this->description = $this->_('Zip banner is a special type of banner that allows you to create zip files with banner images, templates, ... You can use user variables that are raplaced with properiate values. %s<br/>', '<a href="' . Gpf_Application::getKnowledgeHelpUrl('features-and-functions/banners/zip-banner.html') . '" target="_blank">' . $this->_('More help in our Knowledge Base') . '</a>');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
        
        $this->addImplementation('PostAffiliate.BannerFactory.getBannerObjectFromType', 'Pap_Features_ZipBanner_Config', 'getBanner');
        $this->addImplementation('PostAffiliate.BannerForm.load', 'Pap_Features_ZipBanner_Config', 'load');
    }
    
    public function onActivate() {
        $zipDir = new Gpf_Io_File(Gpf_Paths::getInstance()->getFullAccountPath().Pap_Features_ZipBanner_Unziper::ZIP_DIR);
        if ($zipDir->isExists() === false) {
            $zipDir->mkdir();
        }
        $cacheZipDir = new Gpf_Io_File(Gpf_Paths::getInstance()->getCacheAccountDirectory().'zip/');
        if ($cacheZipDir->isExists() === false) {
            $cacheZipDir->mkdir();
        }
    }
    
    public function onDeactivate() {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Pap_Db_Table_Banners::getName());
        $delete->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_ZipBanner_Config::BannerTypeZip);
        $delete->execute();
    }
}
?>
