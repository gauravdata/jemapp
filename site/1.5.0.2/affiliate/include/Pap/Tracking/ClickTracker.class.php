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
class Pap_Tracking_ClickTracker extends Pap_Tracking_TrackerBase {
    const LINKMETHOD_REDIRECT = "R";
    const LINKMETHOD_URLPARAMETERS = "P";
    const LINKMETHOD_MODREWRITE = "S";
    const LINKMETHOD_DIRECTLINK = "D";
    const LINKMETHOD_ANCHOR = "A";
    
    /**
     * @var Pap_Tracking_ClickTracker
     */
    private static $instance = NULL;

    /**
     * @var Pap_Tracking_Cookie
     */
    private $cookies;

    /**
     * @return Pap_Tracking_ClickTracker
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Pap_Tracking_ClickTracker();
        }
        return self::$instance;
    }

    public function __construct(){
        $this->cookies = new Pap_Tracking_Cookie();
    }

    public static function sendJavaScriptHeaders() {
        Gpf_Http::setHeader('Content-Type', 'application/x-javascript');
    }

    public function getLinkingMethod() {
        return Gpf_Settings::get(Pap_Settings::SETTING_LINKING_METHOD);
    }

    /**
     * Gets clickUrl for banner
     *
     * @param Pap_Common_Banner $banner
     * @param Pap_Common_User $user
     * @param string $specialDestinationUrl
     * @return string
     */
    public function getClickUrl(Pap_Common_Banner $banner = null,
                                Pap_Common_User $user,
                                $specialDestinationUrl = '',
                                $flags = '',
                                Pap_Db_Channel $channel = null) {

        if ($banner != null && $banner->getDynamicLink() != null && $flags == self::LINKMETHOD_REDIRECT) {
            return $this->getRedirectClickUrl($banner, $user, $banner->getDynamicLink(), $channel);
        }
        if($flags & Pap_Common_Banner::FLAG_DIRECTLINK) {
            return $this->geDirectLinkClickUrl($banner, $user, $specialDestinationUrl);
        }
        if ($this->getLinkingMethod() == self::LINKMETHOD_REDIRECT) {
            return $this->getRedirectClickUrl($banner, $user, $specialDestinationUrl, $channel);

        } else if ($this->getLinkingMethod() == self::LINKMETHOD_URLPARAMETERS) {
            return $this->getUrlParametersClickUrl($banner, $user, $specialDestinationUrl, $channel);

        } else if ($this->getLinkingMethod() == self::LINKMETHOD_MODREWRITE) {
            return $this->getModRewriteClickUrl($banner, $user, $specialDestinationUrl, $channel);

        } else if ($this->getLinkingMethod() == self::LINKMETHOD_DIRECTLINK) {
            return $this->geDirectLinkClickUrl($banner, $user, $specialDestinationUrl, $channel);

        } else if ($this->getLinkingMethod() == self::LINKMETHOD_ANCHOR) {
            return $this->getAnchorClickUrl($banner, $user, $specialDestinationUrl, $channel);
        }
    }
     
    /**
     * @return String redirect click url (redirect through click.php script)
     */
    private function getRedirectClickUrl(Pap_Common_Banner $banner = null, Pap_Common_User $user, $specialDesturl = '', Pap_Db_Channel $channel = null) {
        $clickUrl = Pap_Tracking_TrackerBase::getScriptUrl("click.php");
        $clickUrl .= "?".Pap_Tracking_Request::getAffiliateClickParamName()."=".$user->getRefId();
        $clickUrl .= $this->getBannerParams($banner);
        if ($specialDesturl != '') {
            $clickUrl .= "&amp;".Pap_Tracking_Request::getSpecialDestinationUrlParamName()."=".urlencode($specialDesturl);
        }
        if($channel != null && is_object($channel)) {
            $clickUrl .= "&amp;".Pap_Tracking_Request::getChannelParamName()."=".$channel->getValue();
        }
        return $clickUrl;
    }

    /**
     * @return String url parameters style click url (requires integration code on landing page)
     */
    private function getUrlParametersClickUrl(Pap_Common_Banner $banner = null,
                                              Pap_Common_User $user,
                                              $specialDesturl = '',
                                              Pap_Db_Channel $channel = null, $urlSeparator = '?') {

        $clickUrl = $this->getDestinationUrl($banner, $specialDesturl, $user);

        $firstParamSeparator = ($urlSeparator === '#' ? $urlSeparator : '&amp;');
        
        $clickUrl .= (strpos($clickUrl, '?') === false) ? $urlSeparator : $firstParamSeparator;
        $clickUrl .= Pap_Tracking_Request::getAffiliateClickParamName()."=".$user->getRefId();
        $clickUrl .= $this->getBannerParams($banner);
        if($channel != null && is_object($channel)) {
            $clickUrl .= "&amp;".Pap_Tracking_Request::getChannelParamName()."=".$channel->getValue();
        }
        return $clickUrl;
    }
    
    private function getAnchorClickUrl(Pap_Common_Banner $banner = null,
                                              Pap_Common_User $user,
                                              $specialDesturl = '',
                                              Pap_Db_Channel $channel = null) {

        return $this->getUrlParametersClickUrl($banner, $user, $specialDesturl, $channel, '#');
    }

    function getBannerParams(Pap_Common_Banner $banner = null){
      $clickUrl = '';
      if ($banner != null) {
            $clickUrl .= "&amp;".Pap_Tracking_Request::getBannerClickParamName()."=".$banner->getId();
            if ($banner->getParentBannerId()!= null) {
                $clickUrl .= "&amp;".Pap_Tracking_Request::getRotatorBannerParamName()."=".$banner->getParentBannerId();
            }
        }
        return $clickUrl;
    }
    
    
    /**
     * @return String seo style click url
     */
    public function getModRewriteClickUrl(Pap_Common_Banner $banner = null,
                                           Pap_Common_User $user,
                                           $specialDesturl = '',
                                           Pap_Db_Channel $channel = null,
                                           $siteUrl = null) {

       	$prefix = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_PREFIX_SETTING_NAME);
        $separator = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_SEPARATOR_SETTING_NAME);
        $suffix = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_SUFIX_SETTING_NAME);
        if ($siteUrl === null) {
            $siteUrl = Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL);
        }

        if($siteUrl[strlen($siteUrl)-1] != '/') {
            $siteUrl .= '/';
        }
        $clickUrl = $siteUrl;
        $clickUrl .= $prefix.$user->getRefId();
        if ($banner != null) {
            $clickUrl .= $separator.$banner->getId();
        }
        if($channel != null && is_object($channel)) {
            $clickUrl .= $separator.$channel->getValue();
        }

        $clickUrl .= $suffix;

        return $clickUrl;
    }

    /**
     * @return String old style click url (redirect through click.php script)
     */
    private function geDirectLinkClickUrl(Pap_Common_Banner $banner = null,
                                          Pap_Common_User $user,
                                          $specialDesturl = '',
                                          Pap_Db_Channel $channel = null) {
                                              
        return $this->getDestinationUrl($banner, $specialDesturl, $user);
    }
    
    private function getDestinationUrl(Pap_Common_Banner $banner = null, $specialDestinationUrl = '', Pap_Common_User $user = null) {
        if ($specialDestinationUrl != '') {
            return $specialDestinationUrl;
        }
        if ($banner != null) {
            return $banner->getDestinationUrl($user);
        }
        return Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL);
    }
}

?>
