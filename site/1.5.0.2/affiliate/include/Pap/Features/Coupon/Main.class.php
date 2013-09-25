<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
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
 * @package PostAffiliatePro plugins
 */
class Pap_Features_Coupon_Main extends Gpf_Plugins_Handler {

    /**
     * @var Pap_Db_Coupon
     */
    private $coupon;
    /**
     * @var Pap_Common_Banner
     */
    private $banner;
    /**
     * @var Pap_Common_Campaign
     */
    private $campaign;
    /**
     * @var Pap_Affiliates_User
     */
    private $affiliate;

    public static function getHandlerInstance() {
        return new Pap_Features_Coupon_Main();
    }

    public function getBanner(Pap_Common_Banner_BannerRequest $bannerRequest) {
        if ($bannerRequest->getType() == Pap_Features_Coupon_Coupon::TYPE_COUPON) {
            $couponBanner = new Pap_Features_Coupon_Coupon();
            $couponBanner->setData3(Pap_Features_Coupon_Coupon::DEFAULT_DESIGN);
            $bannerRequest->setBanner($couponBanner);
        }
    }

    public function addToMenu(Pap_Merchants_Menu $menu) {
        $menu->addItem('Offline-Sale', $this->_('Offline sale'));
        return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    public function recognizeParameters(Pap_Common_VisitorAffiliateCacheCompoundContext $visitorAffiliateCacheCompoundContext) {
        $context = $visitorAffiliateCacheCompoundContext->getContext();
        $context->debug("Recognizing parameters from coupon started");

        $this->coupon = $this->recognizeCoupon($context);
        if ($this->coupon != null) {
            $this->recognizeAffiliate($context);
            $this->recognizeBanner($context);
            $this->recognizeCampaign($context);
            if ($this->isRecognizedParams()) {
                $this->setRecognizedParams($context);
            }
        }

        $context->debug("Recognizing parameters from Coupon ended");
        return Gpf_Plugins_Engine::PROCESS_CONTINUE;
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     * @param $couponCode
     */
    protected function getCoupon($couponCode) {
        $coupon = new Pap_Db_Coupon();
        $coupon->setCode($couponCode);
        $coupon->loadFromCode();
        return $coupon;
    }

    /**
     * @throws Gpf_Exception
     * @return Pap_Affiliates_User
     */
    protected function getAffiliate($userID) {
        return Pap_Affiliates_User::loadFromId($userID);
    }

    /**
     * @throws Gpf_Exception
     * @return Pap_Common_Banner
     */
    protected function getBannerFromFactory($bannerID) {
        $bannerFactory = new Pap_Common_Banner_Factory();
        return $bannerFactory->getBanner($bannerID);
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @param $campaignID
     * @return Pap_Common_Campaign
     */
    protected function getCampaign($campaignID) {
        $campaign = new Pap_Common_Campaign();
        $campaign->setId($this->banner->getCampaignId());
        $campaign->load();
        return $campaign;
    }

    private function isRecognizedParams() {
        if (is_null($this->affiliate) || is_null($this->banner) || is_null($this->campaign)) {
            return false;
        }
        return true;
    }

    /**
     * @param Pap_Contexts_Action $context
     * @return Pap_Db_Coupon
     */
    private function recognizeCoupon(Pap_Contexts_Action $context) {
        $context->debug("Trying to get coupon from request parameter '".Pap_Tracking_ActionRequest::PARAM_ACTION_COUPON."'");

        $couponCode = $context->getCouponFromRequest();
        if($couponCode != '') {
            return $this->checkCouponIsCorrect($context, $couponCode);
        }
        $context->debug("Coupon not found in parameter");
        return null;
    }

    /**
     * checks that coupon with this code exists and is correct
     *
     * @param Pap_Contexts_Action $context
     * @param string $couponCode
     * @param string $trackingMethod
     * @return Pap_Db_Coupon
     */
    private function checkCouponIsCorrect(Pap_Contexts_Action $context, $couponCode) {
        $context->debug("Checking coupon with code: ".$couponCode);
        try {
            $coupon = $this->getCoupon($couponCode);
            $context->debug("Coupon status: " . $coupon->getStaus());
            $context->debug("Coupon validity: " . $coupon->getValidFrom() . " to " . $coupon->getValidTo());
            $context->debug("Max use count: " . $coupon->getMaxUseCount());            
            if ($coupon->isValid()) {
                return $coupon;
            }
            $context->debug("Skipping, coupon with code: $couponCode is invalid");
        } catch (Gpf_Exception $e) {
            $context->debug("Coupon with code: $couponCode doesn't exist");
        }
        return null;
    }

    private function recognizeAffiliate(Pap_Contexts_Action $context) {
        $context->debug("Checking affiliate with Id: ".$this->coupon->getUserID());
        if ($this->coupon->getUserID() != null) {
            try {
                $this->affiliate = $this->getAffiliate($this->coupon->getUserID());
            } catch (Gpf_Exception $e) {
                $context->debug("User with RefId/UserId: " . $this->coupon->getUserID() . " doesn't exist");
            }
        }
    }

    private function recognizeBanner(Pap_Contexts_Action $context) {
        $context->debug("Checking banner with Id: ".$this->coupon->getBannerID());
        if ($this->coupon->getBannerID() != null) {
            try {
                $this->banner = $this->getBannerFromFactory($this->coupon->getBannerID());
            } catch (Gpf_Exception $e) {
                $context->debug("Banner with id: " . $this->coupon->getBannerID() . " doesn't exist");
            }
        }
    }

    private function recognizeCampaign(Pap_Contexts_Action $context) {
        if ($this->banner == null) {
            return;
        }
        $context->debug("Checking campaign with Id: ".$this->banner->getCampaignId());
        if ($this->banner->getCampaignId() != null) {
            try {
                $this->campaign = $this->getCampaign($this->banner->getCampaignId());
            } catch (Gpf_Exception $e) {
                $context->debug("Campaign with id: " . $this->banner->getCampaignId() . " doesn't exist");
            }
        }
    }

    private function setRecognizedParams(Pap_Contexts_Action $context) {
        $context->setUserObject($this->affiliate);
        $context->setBannerObject($this->banner);
        $context->setCampaignObject($this->campaign);
        $context->setAccountId($this->campaign->getAccountId(), Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_FROM_CAMPAIGN);
        $context->setTrackingMethod(Pap_Common_Transaction::TRACKING_METHOD_COUPON);
        $context->getTransaction()->setCouponId($this->coupon->getId());
        $context->getTransaction()->set(Pap_Db_Table_Transactions::BANNER_ID, $this->banner->getId());
    }
}
?>
