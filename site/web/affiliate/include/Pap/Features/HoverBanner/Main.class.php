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

/**
 * @package PostAffiliatePro plugins
 */
class Pap_Features_HoverBanner_Main extends Gpf_Plugins_Handler {

    public static function getHandlerInstance() {
        return new Pap_Features_HoverBanner_Main();
    }

    public function getBanner(Pap_Common_Banner_BannerRequest $bannerRequest) {
        if ($bannerRequest->getType() == Pap_Features_HoverBanner_Hover::TYPE_HOVER) {
            $bannerRequest->setBanner(new Pap_Features_HoverBanner_Hover());
        }
    }
}
?>
