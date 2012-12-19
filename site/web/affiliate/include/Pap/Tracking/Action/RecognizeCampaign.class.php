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
class Pap_Tracking_Action_RecognizeCampaign extends Pap_Tracking_Common_RecognizeCampaign {
    /**
     * @var Pap_Tracking_Action_RecognizeCampaignIdByProductId
     */
    protected $recognizeCampaignIdByProductId;

    public function __construct() {
        $this->recognizeCampaignIdByProductId = new Pap_Tracking_Action_RecognizeCampaignIdByProductId();
    }

    /**
     * @return Pap_Common_Banner
     */
    protected function recognizeCampaigns(Pap_Contexts_Tracking $context) {
        if ($context->getCampaignObject() != null) {
            return $context->getCampaignObject();
        }

        try {
            return $this->getCampaignFromForcedBanner($context);
        } catch (Gpf_Exception $e) {
        }
        
        try {
            return $this->getCampaignFromParameter($context);
        } catch (Gpf_Exception $e) {
        }

        try {
            return $this->getCampaignFromProductID($context);
        } catch (Gpf_Exception $e) {
            if (Gpf_Settings::get(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME) == Gpf::YES) {
                $context->setDoCommissionsSave(false);
                return; 
            }
        }

        try {
            $visitorAffiliate = $context->getVisitorAffiliate();
            if ($visitorAffiliate != null) {
                $context->debug('Getting campaign from visitor affiliate, visitorId: '.$visitorAffiliate->getVisitorId());
                $context->debug('Checking campaign with Id: '.$visitorAffiliate->getCampaignId());
                return $this->getCampaignById($context, $visitorAffiliate->getCampaignId());
            }
        } catch (Gpf_Exception $e) {
        }

        try {
            return $this->getDefaultCampaign($context);
        } catch (Gpf_Exception $e) {
        }
    }

    private function getCampaignFromForcedBanner(Pap_Contexts_Action $context) {
        $banner = $this->getBanner($context->getBannerIdFromRequest());
        return $this->getCampaignById($context, $banner->getCampaignId());
    }
    
    /**
     * @return Pap_Db_Banner
     * @throws Gpf_Exception
     */
    protected function getBanner($bannerId) {
        $banner = new Pap_Db_Banner();
        $banner->setId($bannerId);
        $banner->load();
        return $banner;
    }
    
    /**
     * returns campaign object from campaign ID stored in request parameter
     */
    private function getCampaignFromParameter(Pap_Contexts_Action $context) {
        $context->debug('Trying to get campaign from request parameter '.Pap_Tracking_ActionRequest::PARAM_ACTION_CAMPAIGNID);

        $campaignId = $context->getCampaignIdFromRequest();

        if($campaignId == '') {
            $this->logAndThrow($context, 'Campaign ID request parameter is empty');
        }

        $context->debug('Checking campaign with Id: '.$campaignId);
        return $this->getCampaignById($context, $campaignId);
    }

    /**
     * returns campaign object from Product ID stored in request parameter
     */
    private function getCampaignFromProductID(Pap_Contexts_Action $context) {
        $context->debug('Trying to get campaign from Product ID: '.$context->getProductIdFromRequest());
        return $this->getCampaignById($context, $this->recognizeCampaignIdByProductId->recognizeCampaignId($context, $context->getProductIdFromRequest()));
    }
}

?>
