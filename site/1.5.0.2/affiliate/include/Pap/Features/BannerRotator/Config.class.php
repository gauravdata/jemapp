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
class Pap_Features_BannerRotator_Config extends Gpf_Plugins_Handler {

    const BannerTypeRotator = 'R';

    public static function getHandlerInstance() {
        return new Pap_Features_BannerRotator_Config();
    }

    public function getBanner(Pap_Common_Banner_BannerRequest $bannerRequest) {
        if($bannerRequest->getType()==self::BannerTypeRotator){
            $bannerRequest->setBanner(new Pap_Features_BannerRotator_Rotator());
        }
    }
}

?>
