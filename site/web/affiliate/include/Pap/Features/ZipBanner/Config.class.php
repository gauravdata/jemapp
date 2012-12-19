<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: LanguageAndDate.class.php 18081 2008-05-16 12:17:32Z mfric $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Features_ZipBanner_Config extends Gpf_Plugins_Handler {

    const BannerTypeZip = 'Z';

    public static function getHandlerInstance() {
        return new Pap_Features_ZipBanner_Config();
    }

    public function getBanner(Pap_Common_Banner_BannerRequest $bannerRequest) {
        if($bannerRequest->getType()==self::BannerTypeZip){
            $bannerRequest->setBanner(new Pap_Features_ZipBanner_Zip());
        }
    }
        
    public function load(Gpf_Rpc_Form $form) {
        $form->addField('zipFolder', Gpf_Paths::getInstance()->getFullAccountPath() . Pap_Features_ZipBanner_Unziper::ZIP_DIR . 
            $form->getFieldValue(Pap_Db_Table_Banners::DATA1) . '/');
        $form->addField('accountZipFolder', Gpf_Paths::getInstance()->getFullAccountPath() . Pap_Features_ZipBanner_Unziper::ZIP_DIR);
    }
}

?>
