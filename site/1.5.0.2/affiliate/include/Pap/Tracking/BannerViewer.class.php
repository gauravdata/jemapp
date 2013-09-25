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
class Pap_Tracking_BannerViewer extends Gpf_Object {

    const EXT_POINT_NAME = 'PostAffiliate.BannerViewer.show';

    public function show(Pap_Db_CachedBanner $cachedBanner) {
        try {
            $request = new Pap_Tracking_Request();
            $banner = $this->getBanner($cachedBanner->getBannerId(), $cachedBanner->getUserId(), $cachedBanner->getChannel());
            $req = new Pap_Tracking_BannerViewerRequest($banner->getBannerType());
            Gpf_Plugins_Engine::extensionPoint(self::EXT_POINT_NAME, $req);
            if($req->getViewer() != null) {
                $req->getViewer()->showBanner($request , $banner);
                return;
            }
            $this->prepareCachedBanner($banner, $cachedBanner);
            try {
                $cachedBanner->save();
            } catch (Gpf_DbEngine_Row_ConstraintException $e) {
                // cached banner was saved already by other script
            }
            if ($cachedBanner->getHeaders() != '') {
                header($cachedBanner->getHeaders(), true);
            }
            echo $cachedBanner->getCode();
        } catch (Exception $e) {
            $this->logMessage($e);
            echo $e;
        }
    }

    private function prepareCachedBanner(Pap_Common_Banner $banner, Pap_Db_CachedBanner $cachedBanner) {
        if ($banner->getWrapperId() !== null && $cachedBanner->getWrapper() !== '') {
            Pap_Merchants_Config_BannerWrapperService::fillCachedBanner($banner, $cachedBanner);
            return;
        }
        if ($banner->getBannerType() == Pap_Features_HoverBanner_Hover::TYPE_HOVER) {
            $cachedBanner->setCode($banner->getCode(Pap_Affiliates_User::loadFromId($cachedBanner->getUserId()), Pap_Common_Banner::FLAG_RAW_CODE));
        } else {
            $banner->fillCachedBanner($cachedBanner, Pap_Affiliates_User::loadFromId($cachedBanner->getUserId()));
        }
        self::addJavascriptCode($cachedBanner);
    }
    
    public static function addJavascriptCode(Pap_Db_CachedBanner $cachedBanner) {
        $code = $cachedBanner->getCode();
        $code = str_replace("\n", " ", $code);
        $code = str_replace("\r", " ", $code);
        $code = str_replace("'", "\\'", $code);
        $cachedBanner->setCode("document.write('$code')");
        $cachedBanner->setHeaders('Content-Type: application/x-javascript');
    }

    /**
     * @service banner read
     * @param $fields
     */
    public function getBannerLink(Gpf_Rpc_Params $params){
        $form = new Gpf_Rpc_Form($params);
        $form->addField("link", self::getBannerScriptUrl($params->get('affiliateId'),$params->get('bannerId')));
        return $form;
    }

    public static function getBannerScriptUrl($userId, $bannerId, $channelId = null, $parentBannerId = null){
        $url =  Gpf_Paths::getInstance()->getFullScriptsUrl().'banner.php'.
                    '?';
        $url .= Gpf_Settings::get(Pap_Settings::PARAM_NAME_USER_ID).'='.$userId;
        $url .= '&'.Gpf_Settings::get(Pap_Settings::PARAM_NAME_BANNER_ID).'='.$bannerId;
        if($channelId != null){
            $url .= '&'.Pap_Tracking_Request::getChannelParamName().'='.$channelId;
        }
        if($parentBannerId != null) {
            $url .= '&'.Pap_Tracking_Request::getRotatorBannerParamName().'='.$parentBannerId;
        }
        return $url;
    }

    private function logMessage(Exception $e) {
        Gpf_Log::warning('Trying to show non-existing banner: '. $e->getMessage());
    }

    /**
     * @return Pap_Affiliates_User
     * @throws Gpf_Exception
     */
    private function getUser($userid) {
        return Pap_Affiliates_User::loadFromId($userid);
    }

    /**
     * @return Pap_Common_Banner
     */
    private function getBanner($bannerId, $userId = null, $channelId = '') {
        $bannerFactory = new Pap_Common_Banner_Factory();
        $banner = $bannerFactory->getBanner($bannerId);
        if(isset($_REQUEST[Pap_Db_Table_CachedBanners::DYNAMIC_LINK])) {
            $banner->setDynamicLink($_REQUEST[Pap_Db_Table_CachedBanners::DYNAMIC_LINK]);
        }
        if ($channelId == '' || $userId == null || $userId == '') {
            return $banner;
        }
        try {
            $banner->setChannel($this->getChannel($userId, $channelId));
        } catch (Gpf_Exception $e) {
            Gpf_Log::info('Invalid channel '.$channelId.' used in banner '.$bannerId.' for user '.$userId.': '. $e->getMessage());
        }
        return $banner;
    }
    
    /**
     * @param $userId (can be userid or refid)
     * @param $channelId
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     * @return Pap_Db_Channel
     */
    protected function getChannel($userId, $channelId) {
    	$channel = $this->createChannel();
        $channel->setPapUserId($this->getAffiliate($userId)->getId());
        $channel->setValue($channelId);
        $channel->loadFromData(array(Pap_Db_Table_Channels::USER_ID, Pap_Db_Table_Channels::VALUE));
        return $channel;
    }
    
    /**
     * @throws Gpf_Exception
     * @return Pap_Affiliates_User
     */
    protected function getAffiliate($userId) {
    	return Pap_Affiliates_User::loadFromId($userId);
    }
    
    /**
     * @return Pap_Db_Channel
     */
    protected function createChannel() {
    	return new Pap_Db_Channel();
    }
    
    
    /**
     * @deprecated should be moved to hover banner
     */
    public function showHover() {
        $request = new Pap_Tracking_Request();
        try {
            $banner = $this->getBanner($request->getBannerId(), $request->getAffiliateId(), $request->getChannelId());
            if ($banner->getBannerType() == Pap_Features_HoverBanner_Hover::TYPE_HOVER) {
                return $banner->getDisplayCode($request->getUser());
            }
        } catch (Exception $e) {
            $this->logMessage($e);
        }
    }

    /**
     * @deprecated should be moved to hover banner
     */
    public function previewHover() {
        $request = new Pap_Tracking_Request();
        try {
            $banner = $this->getBanner($request->getBannerId(), $request->getAffiliateId(), $request->getChannelId());
            if ($banner->getBannerType() == Pap_Features_HoverBanner_Hover::TYPE_HOVER) {
                return $banner->getPreviewCode($request->getUser());
            }
        } catch (Exception $e) {
            $this->logMessage($e);
        }
    }
}

?>
