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

/**
 * @package PostAffiliate
 */
class SeoTargetUrlVariable_Main extends Gpf_Plugins_Handler {

    /**
     * @return SeoTargetUrlVariable_Main
     */
    public static function getHandlerInstance() {
        return new SeoTargetUrlVariable_Main();
    }

    public function replaceVariables(Pap_Common_BannerReplaceVariablesContext $context) {
        $seo_targeturl = Pap_Tracking_ClickTracker::getInstance()->getModRewriteClickUrl(
            $context->getBanner(), $context->getUser(), '',
            $context->getBanner()->getChannel(), $context->getBanner()->getDestinationUrl());

        $context->setText(Pap_Common_UserFields::replaceCustomConstantInText('seo_targeturl', $seo_targeturl, $context->getText()));
    }
}
?>
