<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Features_SplitCommissions_ContextProcessor extends Gpf_Object {

    /**
     * @var Pap_Contexts_Action
     */
    private $context;

    private $isValid = false;

    public function __construct(Pap_Contexts_Action $context) {
        $this->context = clone $context;
    }

    /**
     * @return Pap_Contexts_Action
     *
     */
    public function getContext() {
        return $this->context;
    }

    /**
     * @param Pap_Db_VisitorAffiliate
     * @return Pap_Db_VisitorAffiliate
     */
    public function recognizeAffiliate(Pap_Db_VisitorAffiliate $visitorAffiliate) {
        $user = $this->getUserById($visitorAffiliate->getUserId());

        if ($user == null) {
            $this->isValid = false;
            return;
        }
        
        $this->context->setUserObject($user);

        $campaign = $this->getCampaignById($visitorAffiliate->getCampaignId());
        if ($campaign != null && $this->context->getCampaignObject() == null) {
            $this->context->setCampaignObject($campaign);
        }

        $banner = $this->getBannerById($visitorAffiliate->getBannerId());
        if ($banner != null && $this->context->getBannerObject() == null) {
            $this->context->setBannerObject($banner);
        }

        $channel = $this->getChannelById($visitorAffiliate->getChannelId());
        if ($channel != null) {
            $this->context->setChannelObject($channel);
        }
        $this->context->setVisitorAffiliate($visitorAffiliate);

        $this->isValid = true;
    }

    public function isValid() {
        return $this->isValid;
    }

    /**
     * @return Pap_Common_User
     */
    protected function getUserById($userId) {
        try {
            return Pap_Common_User::getUserById($userId);
        } catch (Gpf_Exception $e) {
            return null;
        }
    }

    /**
     * @return Pap_Common_Campaign
     */
    protected function getCampaignById($campaignId) {
        try {
            return Pap_Common_Campaign::getCampaignById($campaignId);
        } catch (Gpf_Exception $e) {
            return null;
        }
    }

    /**
     * @return Pap_Common_Banner
     */
    protected function getBannerById($bannerId) {
        $bannerFactory = new Pap_Common_Banner_Factory();
        try {
            $banner = $bannerFactory->getBanner($bannerId);
            return $banner;
        } catch (Pap_Common_Banner_NotFound $e) {
            return null;
        } catch (Gpf_Exception $e) {
            return null;
        }
    }

    /**
     * @return Pap_Db_Channel
     */
    protected function getChannelById($channelId) {
        $channel = new Pap_Db_Channel();
        $channel->setId($channelId);
        try {
            $channel->load();
            return $channel;
        } catch (Gpf_Exception $e) {
            return null;
        }
    }
}

?>
