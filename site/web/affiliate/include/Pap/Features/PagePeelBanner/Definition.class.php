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

class Pap_Features_PagePeelBanner_Definition extends Gpf_Plugins_Definition  {

    public function __construct() {
        $this->codeName = 'PagePeelBanner';
        $this->name = $this->_('Page Peel Banner');
        $this->description = $this->_('Peelbanners are also known as magic corners or pagepeels. They are displayed as a small animated corner of the page and wakes the curiosity of most users. Only if the user moves his mouse onto, it peels impressively over the real homepage.');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;

        $this->addImplementation('PostAffiliate.BannerFactory.getBannerObjectFromType',
		 'Pap_Features_PagePeelBanner_Handler', 'getBanner');
        $this->addImplementation(Pap_Tracking_BannerViewer::EXT_POINT_NAME,
        'Pap_Features_PagePeelBanner_Handler' , 'processViewerRequest');
    }

    public function onDeactivate() {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Pap_Db_Table_Banners::getName());
        $delete->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_PagePeelBanner_Handler::TYPE);
        $delete->execute();
    }
}
?>
