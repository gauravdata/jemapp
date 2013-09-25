<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Rene Dohan
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */
class Pap_Features_RebrandPdfBanner_Definition extends Gpf_Plugins_Definition  {

    public function __construct() {
        $this->codeName = 'RebrandPdfBanner';
        $this->name = $this->_('RebrandPdf Banner');
        $this->description = $this->_('It alows you to create a special type of promo materials - rebrandable e-books. You can create your PDF e-book only once and upload it to %s.Your affiliates will automatically get their own re-branded copy of the book, with their name, affiliate links, texts, etc. There is no need to run any Windows program for branding, they will simply download rebranded PDF. All the rebranding is made automatically in the background.<br/><a href="%s" target="_blank">%s</a>', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO), Gpf_Application::getKnowledgeHelpUrl('features-and-functions/rebrand-pdf-banner.html') ,$this->_('Read more in our Knowledge Base'));
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.BannerFactory.getBannerObjectFromType'
        , 'Pap_Features_RebrandPdfBanner_Config', 'getBanner');
        $this->addImplementation(Pap_Tracking_BannerViewer::EXT_POINT_NAME,
        'Pap_Features_RebrandPdfBanner_Config' , 'processRequest');
    }

    public function onDeactivate() {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Pap_Db_Table_Banners::getName());
        $delete->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_RebrandPdfBanner_Config::TYPE);
        $delete->execute();
    }

    /**
     * Method will be called, when plugin is activated. e.g. create some tables required by plugin.
     *
     * @throws Gpf_Exception when plugin can not be activated
     */
    public function onActivate() {
        if (Gpf_Paths::getInstance()->isDevelopementVersion()) {
            return;
        }
        if(!Gpf_Php::isFunctionEnabled('iconv')) {
            throw new Gpf_Exception($this->_('Please enable "iconv" extension.'));
        }
        if(!Gpf_Php::isExtensionLoaded('ionCube Loader')) {
            throw new Gpf_Exception($this->_('Please enable "ionCube Loader" extension.'));
        }
    }
}
?>
