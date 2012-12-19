<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
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
class Pap_Contexts_Impression extends Pap_Contexts_Tracking {
    
    private $count = 1;
    
    /**
     * @var Pap_Db_RawImpression
     */
    private $rawImpression;

    public function __construct(Pap_Db_RawImpression $rawImpression) {
    	parent::__construct();
    	$this->rawImpression = $rawImpression;
    }
    
    public function setCount($count) {
    	$this->count = $count;
    }

    public function getCount() {
    	return $this->count;
    }

    protected function getActionTypeConstant() {
    	return Pap_Common_Constants::TYPE_CPM;
    }

    public function getCountryCode() {
        return $this->initCountryCode($this->rawImpression);
    }

    public function getClickData1() {
        return $this->rawImpression->getData1();
    }

    public function getClickData2() {
        return $this->rawImpression->getData2();
    }

    public function getDate() {
        return $this->rawImpression->getDate();
    }

    public function isUnique() {
        return $this->rawImpression->isUnique();
    }
    
    public function getBannerId() {
        $bannerObj = $this->getBannerObject();
        if($bannerObj != null) {
            return $bannerObj->getId();
        }
        return null;
    }

    public function getCampaignId() {
        $bannerObj = $this->getBannerObject();
        if($bannerObj != null) {
            return $bannerObj->getCampaignId();
        }
        return null;
    }
    
    public function getParentBannerId() {
        return $this->rawImpression->getParentBannerId();
    }

    public function getChannelId() {
        $channelObj = $this->getChannelObject();
        if($channelObj != null) {
            return $channelObj->getId();
        }
        return null;
    }
    
}
?>
