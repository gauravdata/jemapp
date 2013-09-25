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
class Pap_Tracking_Click_RecognizeDirectLink extends Gpf_Object {

    private $campaignRecognizer;

    public function __construct(Pap_Tracking_Common_RecognizeCampaign $campaignRecognizer = null) {
        if ($campaignRecognizer == null) {
            $this->campaignRecognizer = new Pap_Tracking_Common_RecognizeCampaign();
        } else {
            $this->campaignRecognizer = $campaignRecognizer;
        }
    }

    /**
     * @anonym
     * @service direct_link read
     */
    public function getAffiliateId(Pap_Contexts_Click $context, Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $context = new Pap_Contexts_Click();
        $context->getRequestObject()->setRequestParameter(
        Pap_Tracking_Request::PARAM_REFERRERURL_NAME,
        $params->get('url'));
        $match = $this->getMatch($context);

        if ($match != null) {
            foreach ($match as $key => $value) {
                $data->setValue($key, $value);
            }
        }
        return $data;
    }

    public function process(Pap_Contexts_Click $context, $referrerUrl) {
        $context->debug('DirectLink recognition started');

        $match = $this->getMatch($context, $referrerUrl);
        if($match != null) {
            $context->debug('  Match found, continue processing');
            $this->fillParametersFromMatch($context, $match);
        } else {
            $context->debug('  Match not found, stopping');
            $context->setDoTrackerSave(false);
        }
        	
        $context->debug('DirectLink recognition ended');
    }

    /**
     * recognizes match from referrer by DirectLink feature
     *
     * @return array
     */
    protected function getMatch(Pap_Contexts_Click $context, $referrerUrl) {
        if(Gpf_Settings::get(Pap_Settings::SUPPORT_DIRECT_LINKING) != Gpf::YES) {
            $context->debug('  DirectLink tracking is not supported');
            return null;
        }

        if($referrerUrl == '') {
            $context->debug('Referrer URL empty');
            return null;
        }
        	
        $context->debug('  Trying to recognize affiliate from referrer URL (DirectLink): \'' . $referrerUrl . '\'');
        $directLinksBase = Pap_Tracking_DirectLinksBase::getInstance();
        try {
            $match = $directLinksBase->checkDirectLinkMatch($referrerUrl);
            return $match;
        } catch(Gpf_Exception $e) {
            $context->debug('Exception :'.$e->getMessage());
            return null;
        }
    }

    /**
     * processes match and sets userid, campaign, banner, channel
     */
    protected function fillParametersFromMatch(Pap_Contexts_Click $context, $match) {
        if($match == null || $match == false || !is_array($match) || count($match) != 5) {
            $context->debug("    Matching data are in incorrect format");
        }

        $userId = $match['userid'];
        $url = $match['url'];
        $channelid = $match['channelid'];
        $campaignid = $match['campaignid'];
        $bannerid = $match['bannerid'];

        $context->debug("    Referrer matched '$url' pattern");

        // user
        if ($userId == '') {
            $context->debug("    DirectLink affiliate Id is empty stopping");
            $context->setDoTrackerSave(false);
            return;
        }

        try {
            $user = Pap_Affiliates_User::loadFromId($userId);
        } catch (Gpf_Exception $e) {
            $context->debug(" DirectLink affiliate with id '$userId' doesn't exist");
            $context->setDoTrackerSave(false);
            return;
        }

        $context->debug("    Setting affiliate from referrer URL. Affiliate Id: ".$userId."");
        $context->setUserObject($user);


        // banner
        $banner = null;
        try {
            $bannerFactory = new Pap_Common_Banner_Factory();
            $banner = $bannerFactory->getBanner($bannerid);
            $context->debug("Setting banner from referrer URL. Banner Id: ".$bannerid."");
            $context->setBannerObject($banner);
        } catch (Gpf_Exception $e) {
            $context->debug("Banner parameter in DirectLink is empty");
        }

        // campaign
        $campaign = $this->getCampaignById($context, $campaignid);
        if($campaignid != '' && $campaign != null) {
            $context->debug("    Setting campaign from DirectLink. Campaign Id: ".$campaignid."");
            $context->setCampaignObject($campaign);
        } else {
            $context->debug("    Campaign parameter in DirectLink is empty");
        }
        	
        if($banner != null) {
            $context->debug("    Trying to get campaign from banner");
            $campaign = $this->getCampaignFromBanner($context, $banner);
        }

        if($campaign == null) {
            $campaign = $this->getDefaultCampaign($context);
        }

        if($campaign != null) {
            $context->setCampaignObject($campaign);
        } else {
            $context->setDoTrackerSave(false);
            $context->debug("        No default campaign defined");
        }
        
        // channel
        $channel = $this->getChannelById($context, $channelid);
        if($channelid != '' && $channel != null) {
            $context->debug("    Setting channel from referrer URL. Channel Id: ".$channelid."");
            $context->setChannelObject($channel);
        } else {
            $context->debug("    Channel parameter in DirectLink is empty");
        }
        
        // account
        if ($campaign != null) {
            $context->setAccountId($campaign->getAccountId(), Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_FROM_CAMPAIGN);
        }
    }

    private function getChannelById(Pap_Contexts_Click $context, $channelid) {
        $channelRecognizer = new Pap_Tracking_Click_RecognizeChannel();
        try {
            return $channelRecognizer->getChannelById($context, $channelid);
        } catch (Gpf_Exception $e) {
            return null;
        }
    }

    private function getCampaignFromBanner(Pap_Contexts_Click $context, $banner) {
        $campaignId = $banner->getCampaignId();
        if($campaignId != '') {
            $context->debug("    Setting campaign. Campaign Id: ".$campaignId);
            return $this->getCampaignById($context, $campaignId);
        }

        $context->debug("    Campaign not found");
        return null;
    }

    private function getCampaignById(Pap_Contexts_Click $context, $campaignId) {
        try {
            return $this->campaignRecognizer->getCampaignById($context, $campaignId);
        } catch (Gpf_Exception $e) {
            return null;
    }
    }

    private function getDefaultCampaign(Pap_Contexts_Click $context) {
        try {
            return $this->campaignRecognizer->getDefaultCampaign($context);
        } catch (Gpf_Exception $e) {
            return null;
        }
    }
}

?>
