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
class Pap_Contexts_Click extends Pap_Contexts_Tracking {

    public function __construct() {
    	parent::__construct();
    }

    protected function getActionTypeConstant() {
    	return Pap_Common_Constants::TYPE_CLICK;
    }

	/**
	 * sets click status
     */
	public function setClickStatus($value) {
		$this->set("clickStatus", $value);
	}

	/**
	 * gets click status
	 * @return string
     */
	public function getClickStatus() {
		return $this->get("clickStatus");
	}

	/**
	 * sets click tracking type
     */
	public function setClickTrackingType($value) {
		$this->set("clickTrackingType", $value);
	}

	/**
	 * gets click tracking type
	 * @return string
     */
	public function getClickTrackingType() {
		return $this->get("clickTrackingType");
    }

	/**
	 * gets raw click object (instance of Pap_Db_RawClick)
	 * @return Pap_Db_RawClick
     */
    public function getRawClickObject() {
		return $this->get("rawClickObject");
	}

	/**
	 * sets raw click object (instance of Pap_Db_RawClick)
     */
	public function setRawClickObject(Pap_Db_RawClick $value) {
		$this->set("rawClickObject", $value);
    }

    public function getIp() {
        if ($this->visit != null) {
            return $this->visit->getIp();
        }
        return $this->getRequestObject()->getIP();
    }

    public function getForcedBannerId() {
        return $this->getRequestObject()->getForcedBannerId();
    }

    public function getForcedCampaignId() {
        return $this->getRequestObject()->getForcedCampaignId();
    }

    public function getCampaignId() {
        return $this->getRequestObject()->getCampaignId();
    }

    public function getForcedAffiliateId() {
        return $this->getRequestObject()->getRequestParameter(Pap_Tracking_Request::getForcedAffiliateParamName());
    }

    public function getAffiliateId() {
        return $this->getRequestObject()->getRequestParameter(Pap_Tracking_Request::getAffiliateClickParamName());
    }

    public function getBannerId() {
        return $this->getRequestObject()->getBannerId();
    }

    public function getRotatorBannerId() {
        return $this->getRequestObject()->getRequestParameter(Pap_Tracking_Request::getRotatorBannerParamName());
    }

    public function getForcedChannelId() {
        return $this->getRequestObject()->getForcedChannelId();
    }

    public function getChannelId() {
        return $this->getRequestObject()->getChannelId();
    }

    public function getExtraDataFromRequest($i) {
        if ($i == 1) {
            return $this->getRequestObject()->getClickData1();
        }

        if ($i == 2) {
            return $this->getRequestObject()->getClickData2();
        }
    }

    public function getUserAgent() {
        if ($this->getVisit() == null || $this->getVisit()->getUserAgent() == '') {
            return '';
        }
        return substr(md5($this->getVisit()->getUserAgent()), 0, 6);
    }
}
?>
