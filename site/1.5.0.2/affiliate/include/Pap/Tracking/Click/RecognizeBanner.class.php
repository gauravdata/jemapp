<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
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
class Pap_Tracking_Click_RecognizeBanner extends Pap_Tracking_Common_RecognizeBanner {

    /**
     * @return Pap_Common_Banner
     */
    protected function recognizeBanners(Pap_Contexts_Tracking $context) {
        try {
            return $this->getBannerFromForcedParameter($context);
        } catch (Gpf_Exception $e) {
        }
        
        try {
            return $this->getBannerFromParameter($context);
        } catch (Gpf_Exception $e) {
        }

        return null;
    }

    /**
     * returns user object from forced parameter AffiliateID
     * parameter name is dependent on track.js, where it is used.
     *
     * @return Pap_Common_Banner
     * @throws Gpf_Exception
     */
    private function getBannerFromForcedParameter(Pap_Contexts_Click $context) {
        $id = $context->getForcedBannerId();

        if($id == '') {
            $message = 'Banner id not found in forced parameter';
            $context->debug($message);
            throw new Pap_Tracking_Exception($message);
        }

        $context->debug("Getting banner from forced request parameter. Banner Id: ".$id);
        return $this->getBannerById($context, $id);
    }
}

?>
