<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Features_BannerRotator_Tracking extends Gpf_Plugins_Handler {

    public static function getHandlerInstance() {
        return new Pap_Features_BannerRotator_Tracking();
    }

    public function getAllImpressionsForProcessing(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
        $selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::PARENTBANNERID);
    }

    public function saveRotatorImpression(Pap_Contexts_Impression $context) {
        $context->debug('FEATURE BannerRotator: saveRotatorImpression started');

        $banner = $context->getBannerObject();
        if($banner != null && $context->getParentBannerId() != null){
            $banner->setParentBannerId($context->getParentBannerId());
            $context->debug('&nbsp;&nbsp;FEATURE BannerRotator: Saving rotator impression');
            $this->saveChildImpression($banner, $context);
        } else {
            $context->debug('&nbsp;&nbsp;FEATURE BannerRotator: Banner is not in rotator, skipping');
        }

        $context->debug('FEATURE BannerRotator: saveRotatorImpression ended');
        $context->debug('');
        return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    public function saveRotatorClick(Pap_Contexts_Click $context) {
        $context->debug('FEATURE BannerRotator: saveRotatorClick started');

        if($context->getDoTrackerSave()) {
            $banner = $context->getBannerObject();
            if($banner != null && is_object($banner) && $banner->getParentBanner()!=null){
                $context->debug('&nbsp;&nbsp;FEATURE BannerRotator: Saving rotator click');
                $this->saveChildClick($banner, $context);
            } else {
                $context->debug('&nbsp;&nbsp;FEATURE BannerRotator: Banner is not in rotator, skipping');
            }
        } else {
            $context->debug('FEATURE BannerRotator: Saving in Tracker is disabled (getDoTrackerSave() returned false), continuing without saving');
        }

        $context->debug('FEATURE BannerRotator: saveRotatorClick ended');
        $context->debug('');
        return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    private function getBannerId(Pap_Contexts_Impression $context) {
        $bannerObj = $context->getBannerObject();
        if($bannerObj != null) {
            return $bannerObj->getId();
        }
        return null;
    }
        
    protected function saveChildImpression(Pap_Common_Banner $banner, Pap_Contexts_Tracking $context) {
    	try {
    		$parentBanner = $banner->getParentBanner();
    		$parentBanner->saveChildImpression($banner, $context->isUnique());	
    	} catch (Gpf_DbEngine_NoRowException $e) {
    		$this->setParentBannerNotExistsDebug($context);
    	}    	
    }
    
    protected function saveChildClick(Pap_Common_Banner $banner, Pap_Contexts_Tracking $context) {
		try {
    		$parentBanner = $banner->getParentBanner();
    		$parentBanner->saveChildClick($banner);	
    	} catch (Gpf_DbEngine_NoRowException $e) {
    		$this->setParentBannerNotExistsDebug($context);
    	}    	
    }
    
    protected function setParentBannerNotExistsDebug(Pap_Contexts_Tracking $context) {
    	$context->debug('&nbsp;&nbsp;FEATURE BannerRotator: Parent banner is not exists, skipping');
    }
}

?>
