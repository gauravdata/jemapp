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
class Pap_Tracking_Click_RecognizeCampaign extends Pap_Tracking_Common_RecognizeCampaign {

    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    private $visitorAffiliateCache;

    public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
        $this->visitorAffiliateCache = $visitorAffiliateCache;
    }

    protected function onCampaignRecognized(Pap_Common_Campaign $campaign, Pap_Contexts_Tracking $context) {
        parent::onCampaignRecognized($campaign, $context);
        $this->visitorAffiliateCache->setAccountId($context->getAccountId());
    }

    /**
     * @return Pap_Common_Banner
     */
    protected function recognizeCampaigns(Pap_Contexts_Tracking $context) {
        if ($context->getCampaignObject() != null) {
            $context->debug('Campaign already recognized, skipping. CampaignId: '.$context->getCampaignObject()->getId());
            return $context->getCampaignObject();
        }

        try {
            return $this->getCampaignFromForcedParameter($context);
        } catch (Gpf_Exception $e) {
        }
        	
        try {
            return $this->getCampaignFromParameter($context);
        } catch (Gpf_Exception $e) {
        }

        try {
            return $this->recognizeCampaignFromBanner($context);
        } catch (Gpf_Exception $e) {
        }

        try {
            return $this->getDefaultCampaign($context);
        } catch (Gpf_Exception $e) {
        }
    }

    /**
     * returns user object from forced parameter CampaignID
     * parameter name is dependent on track.js, where it is used.
     *
     * @return Pap_Common_Campaign
     * @throws Gpf_Exception
     */
    protected function getCampaignFromForcedParameter(Pap_Contexts_Tracking $context) {
        $campaignId = $context->getForcedCampaignId();
        if($campaignId != '') {
            $context->debug("Getting campaign from forced parameter. Campaign Id: ".$campaignId);
            return $this->getCampaignById($context, $campaignId);
        }
        $this->logAndThrow($context, 'Campaign not found in forced parameter');
    }

    /**
     * returns campaign object from standard parameter from request
     *
     * @return string
     */
    protected function getCampaignFromParameter(Pap_Contexts_Tracking $context) {
        $campaignId = $context->getCampaignId();
        if($campaignId != '') {
            $context->debug("Getting affiliate from request parameter. Campaign Id: ".$campaignId);
            return $this->getCampaignById($context, $campaignId);
        }
        $this->logAndThrow($context, "Campaign not found in parameter");
    }

    /**
     * if banner was recognized, get campaign from this banner
     *
     * @param Pap_Plugins_Tracking_Click_Context $context
     * @return unknown
     */
    protected function recognizeCampaignFromBanner(Pap_Contexts_Tracking $context) {
        $banner = $context->getBannerObject();
        if($banner == null) {
            $this->logAndThrow($context, 'Banner is null, cannot recognize campaign');
        }

        $context->debug('Banner recognized, Banner Id: '.$banner->getId().', getting campaign for this banner, campaignId: '. $banner->getCampaignId());
        return $this->getCampaignById($context, $banner->getCampaignId());
    }
}

?>
