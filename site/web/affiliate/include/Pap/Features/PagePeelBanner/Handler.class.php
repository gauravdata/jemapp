<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Rene Dohan
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
class Pap_Features_PagePeelBanner_Handler extends Gpf_Plugins_Handler implements Pap_Common_Banner_Viewer {

    const TYPE = 'Y';

    public static function getHandlerInstance() {
        return new Pap_Features_PagePeelBanner_Handler();
    }

    public function getBanner(Pap_Common_Banner_BannerRequest $request) {
        if($request->getType() == self::TYPE){
            $request->setBanner(new Pap_Features_PagePeelBanner_Banner());
        }
    }

    public function processViewerRequest(Pap_Tracking_BannerViewerRequest  $request){
        if($request->getType() == self::TYPE){
            $request->setViewer($this);
        }
    }

    public function showBanner(Pap_Tracking_Request $request, Pap_Common_Banner $banner) {
        if($request->getAffiliateId() === 'preview'){
            echo $banner->getWindowPreview();
        }
    }
}

?>
