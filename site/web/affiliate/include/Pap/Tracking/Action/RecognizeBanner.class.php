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
class Pap_Tracking_Action_RecognizeBanner extends Gpf_Object implements Pap_Tracking_Common_Recognizer  {

    private $bannerCache = array();

    public function __construct() {
    }

    /**
     * @return Pap_Common_Banner
     */
    public function recognize(Pap_Contexts_Tracking $context) {
        if ($context->getBannerObject() != null) {
            return;
        }
        
        try {
            $context->setBannerObject($this->getBannerById($context->getBannerIdFromRequest()));
            return;
        } catch (Exception $e) {
        }

        try {
            $bannerId = $context->getVisitorAffiliate()->getBannerId();
            $context->setBannerObject($this->getBannerById($bannerId));
        } catch (Exception $e) {
            $context->debug('Banner not recognized');
            return;
        }
    }

    /**
     * @throws Gpf_Exception
     * @return Pap_Common_Banner
     */
    protected function getBannerById($bannerId) {
        if (!$bannerId) {
            throw new Gpf_Exception('Undefined bannerid in Pap_Tracking_Action_RecognizeBanner::getBannerById()');
        }
        if (isset($bannerCache[$bannerId])) {
            return $bannerCache[$bannerId];
        }

        $banner = new Pap_Common_Banner();
        $banner->setId($bannerId);
        $banner->loadFromData();
        $bannerCache[$bannerId] = $banner;
        return $banner;
    }
}

?>
