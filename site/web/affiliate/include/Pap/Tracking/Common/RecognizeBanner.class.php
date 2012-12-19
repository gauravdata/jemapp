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
abstract class Pap_Tracking_Common_RecognizeBanner extends Gpf_Object implements Pap_Tracking_Common_Recognizer {

    private $bannersCache = array();

    public function __construct() {
    }

    public final function recognize(Pap_Contexts_Tracking $context) {
        $context->debug('Recognizing banner started');

        $banner = $this->recognizeBanners($context);

        if($banner == null) {
            $context->debug('  No banner recognized!');
        }
         
        $context->debug('Recognizing banner ended');

        if($banner != null) {
            $this->setParentBanner($context, $banner);
            $context->setBannerObject($banner);
        }
    }

    /**
     * @return Pap_Common_Banner
     */
    protected abstract function recognizeBanners(Pap_Contexts_Tracking $context);

    /**
     *
     * @param Pap_Common_Banner $banner
     */
    private function setParentBanner(Pap_Contexts_Tracking $context, Pap_Common_Banner $banner){
        $id = $context->getRotatorBannerId();
        if($id != ''){
            $banner->setParentBannerId($id);
        }
    }

    /**
     * returns user object from standard parameter from request
     *
     * @return Pap_Common_Banner
     * @throws Gpf_Exception
     */
    protected function getBannerFromParameter(Pap_Contexts_Tracking $context) {
        $id = $context->getBannerId();
        if($id == '') {
            $message = 'Banner id not found in parameter';
            $context->debug($message);
            throw new Pap_Tracking_Exception($message);
        }
        $context->debug("Getting banner from request parameter. Banner Id: ".$id);
        return $this->getBannerById($context, $id);
    }

    /**
     * @return Pap_Common_Banner
     * @throws Gpf_Exception
     */
    protected function getBannerById(Pap_Contexts_Tracking $context, $id) {
        if (isset($this->bannersCache[$id])) {
            return $this->bannersCache[$id];
        }

        $bannerFactory = new Pap_Common_Banner_Factory();
        $banner = $bannerFactory->getBanner($id);
        $this->checkBanner($context, $banner);        
        $this->bannersCache[$id] = $banner;
        return $this->bannersCache[$id];
    }
    
    private function checkBanner(Pap_Contexts_Tracking $context, Pap_Common_Banner $banner) {
        if ($this->isAccountRecognizedNotFromDefault($context) && $banner->getAccountId() != $context->getAccountId()) {
            $context->debug("Banner with Id: ".$banner->getId()." and name '".$banner->getName()."' cannot be used with accountId: '". $context->getAccountId() ."'!");
            throw new Gpf_Exception("Banner is from differen account");
        }
    }
    
    private function isAccountRecognizedNotFromDefault(Pap_Contexts_Tracking $context) {
        if ($context->getAccountId() != null && $context->getAccountRecognizeMethod() != Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_DEFAULT) {
            return true;
        } 
        return false;
    }
}

?>
