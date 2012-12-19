<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 * @deprecated backward compatibility class. use Pap_Api_Tracker instead
 */
class Pap_Api_ClickTracker extends Pap_Api_Tracker {
    
    private $affiliateId;
    private $bannerId;
    private $campaignId;
    private $data1;
    private $data2;
    private $channel;
    
    /**
     * This class requires correctly initialized merchant session
     * @param Gpf_Api_Session $session
     */
    public function __construct(Gpf_Api_Session $session) {
        parent::__construct($session);
    }
    
        /**
     * Use this function if you want to explicitly specify affiliate which made the click
     *
     * @param $affiliateId
     */
    public function setAffiliateId($affiliateId) {
        $this->affiliateId = $affiliateId;
    }

    /**
     * Use this function if you want to explicitly specify banner through which the click was made
     *
     * @param $bannerId
     */
    public function setBannerId($bannerId) {
        $this->bannerId = $bannerId;
    }

    /**
     * Use this function if you want to explicitly specify campaign for this click
     *
     * @param $campaignId
     */
    public function setCampaignID($campaignId) {
        $this->campaignId = $campaignId;
    }

    public function setData1($data1) {
        $this->data1 = $data1;
    }

    public function setData2($data2) {
        $this->data2 = $data2;
    }

    /**
     * Use this function if you want to explicitly specify channel through which this click was made
     *
     * @param $bannerId
     */
    public function setChannel($channel) {
        $this->channel = $channel;
    }
    
    /**
     * @return Gpf_Net_Http_Request
     */
    protected function getGetParams() {
        $getParams = parent::getGetParams();
        if ($this->affiliateId != '') {
            $getParams->addQueryParam('AffiliateID', $this->affiliateId);
        }
        if ($this->bannerId != '') {
            $getParams->addQueryParam('BannerID', $this->bannerId);
        }
        if ($this->campaignId != '') {
            $getParams->addQueryParam('CampaignID', $this->campaignId);
        }
        if ($this->channel != '') {
            $getParams->addQueryParam('chan', $this->channel);
        }
        if ($this->data1 != '') {
            $getParams->addQueryParam('pd1', $this->data1);
        }
        if ($this->data2 != '') {
            $getParams->addQueryParam('pd2', $this->data2);
        }
        return $getParams;
    }
}
?>
