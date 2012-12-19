<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Request.class.php 31175 2011-02-11 10:11:51Z jsimon $
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

class Pap_Tracking_Request extends Gpf_Object {
    const PARAM_CAMPAIGN_ID_SETTING_NAME = 'campaignId';

    /* other action parameters */
    const PARAM_ACTION_DEBUG = 'PDebug';
    const PARAM_CALL_FROM_JAVASCRIPT = 'cjs';

    /* Constant param names */
    const PARAM_LINK_STYLE = 'ls';
    const PARAM_REFERRERURL_NAME = 'refe';

    /* Param setting names */
    const PARAM_DESTINATION_URL_SETTING_NAME = 'param_name_extra_data3';
    const PARAM_CHANNEL_DEFAULT = 'chan';
    const PARAM_CURRENCY = 'cur';

    /* Forced parameter names */
    const PARAM_FORCED_AFFILIATE_ID = 'AffiliateID';
    const PARAM_FORCED_BANNER_ID = 'BannerID';
    const PARAM_FORCED_CAMPAIGN_ID = 'CampaignID';
    const PARAM_FORCED_CHANNEL_ID = 'Channel';
    const PARAM_FORCED_IP = 'Ip';

    private $countryCode;

    protected $request;

    /**
     * @var Gpf_Log_Logger
     */
    protected $logger;

    function __construct() {
        $this->request = $_REQUEST;
    }

    public function parseUrl($url) {
        $this->request = array();
        if ($url === null || $url == '') {
            return;
        }
        $parsedUrl = @parse_url('?'.ltrim($url, '?'));
        if ($parsedUrl === false || !array_key_exists('query', $parsedUrl)) {
            return;
        }
        $args = explode('&', @$parsedUrl['query']);
        foreach ($args as $arg) {
            $parts = explode('=', $arg, 2);
            if (count($parts) == 2) {
                $this->request[$parts[0]] = $parts[1];
            }
        }
    }

    public function getAffiliateId() {
        return $this->getRequestParameter(self::getAffiliateClickParamName());
    }

    public function getForcedAffiliateId() {
        return $this->getRequestParameter(self::getForcedAffiliateParamName());
    }

    public function getBannerId() {
        return $this->getRequestParameter(self::getBannerClickParamName());
    }

    public function getForcedBannerId() {
        return $this->getRequestParameter(self::getForcedBannerParamName());
    }

    /**
     * @return Pap_Common_User
     */
    public function getUser() {
        try {
            return Pap_Affiliates_User::loadFromId($this->getRequestParameter($this->getAffiliateClickParamName()));
        } catch (Gpf_Exception $e) {
            return null;
        }
    }

    /**
     * @param string $id
     * @return string
     */
    public function getRawExtraData($i) {
        $extraDataParamName = $this->getExtraDataParamName($i);
        if (!isset($this->request[$extraDataParamName])) {
            return '';
        }
        $str = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;",urldecode($this->request[$extraDataParamName]));
        return html_entity_decode($str,null,'UTF-8');
    }

    public function setRawExtraData($i, $value) {
        $extraDataParamName = $this->getExtraDataParamName($i);
        $this->request[$extraDataParamName] = $value;
    }

    /**
     * returns custom click link parameter data1
     * It first checks for forced parameter Data1 given as parameter to JS tracking code
     *
     * @return string
     */
    public function getClickData1() {
        $value = $this->getRequestParameter('pd1');
        if($value != '') {
            return $value;
        }

        $paramName = $this->getClickData1ParamName();
        if (!isset($this->request[$paramName])) {
            return '';
        }
        return $this->request[$paramName];
    }

    /**
     * returns custom click link parameter data2
     * It first checks for forcet parameter Data2 given as parameter to JS tracking code
     *
     * @return string
     */
    public function getClickData2() {
        $value = $this->getRequestParameter('pd2');
        if($value != '') {
            return $value;
        }

        $paramName = $this->getClickData2ParamName();
        if (!isset($this->request[$paramName])) {
            return '';
        }
        return $this->request[$paramName];
    }

    public function getClickData1ParamName() {
        return Gpf_Settings::get(Pap_Settings::PARAM_NAME_EXTRA_DATA.'1');
    }

    public function getClickData2ParamName() {
        return Gpf_Settings::get(Pap_Settings::PARAM_NAME_EXTRA_DATA.'2');
    }

    public function getRefererUrl() {
        if (isset($this->request[self::PARAM_REFERRERURL_NAME]) && $this->request[self::PARAM_REFERRERURL_NAME] != '') {
            return self::decodeRefererUrl($this->request[self::PARAM_REFERRERURL_NAME]);
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            return self::decodeRefererUrl($_SERVER['HTTP_REFERER']);
        }
        return '';
    }

    public function getIP() {
        if ($this->getForcedIp() !== '') {
            return $this->getForcedIp();
        }
        return Gpf_Http::getRemoteIp();
    }

    public function getCountryCode() {
        if ($this->countryCode === null) {
            $context = new Gpf_Data_Record(
            array(Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_Impressions::COUNTRYCODE), array($this->getIP(), ''));
            Gpf_Plugins_Engine::extensionPoint('Tracker.request.getCountryCode', $context);
            $this->countryCode = $context->get(Pap_Db_Table_Impressions::COUNTRYCODE);
        }
        return $this->countryCode;
    }

    public function getBrowser() {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return '';
        }
        return substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 6);
    }

    public function getLinkStyle() {
        if (!isset($this->request[self::PARAM_LINK_STYLE]) || $this->request[self::PARAM_LINK_STYLE] != '1') {
            return Pap_Tracking_ClickTracker::LINKMETHOD_REDIRECT;
        }
        return Pap_Tracking_ClickTracker::LINKMETHOD_URLPARAMETERS;
    }

    /**
     * set logger
     *
     * @param Gpf_Log_Logger $logger
     */
    public function setLogger($logger) {
        $this->logger = $logger;
    }

    protected function debug($msg) {
        if($this->logger != null) {
            $this->logger->debug($msg);
        }
    }

    public function getRequestParameter($paramName) {
        if (!isset($this->request[$paramName])) {
            return '';
        }
        return $this->request[$paramName];
    }

    public function setRequestParameter($paramName, $value) {
        $this->request[$paramName] = $value;
    }

    static public function getRotatorBannerParamName() {
        return Gpf_Settings::get(Pap_Settings::PARAM_NAME_ROTATOR_ID);
    }

    static public function getSpecialDestinationUrlParamName() {
        return Gpf_Settings::get(Pap_Settings::PARAM_NAME_DESTINATION_URL);
    }

    public function getRotatorBannerId() {
        return $this->getRequestParameter(self::getRotatorBannerParamName());
    }

    public function getExtraDataParamName($i) {
        return Gpf_Settings::get(Pap_Settings::PARAM_NAME_EXTRA_DATA).$i;
    }

    public function getDebug() {
        if(isset($_GET[self::PARAM_ACTION_DEBUG])) {
            return strtoupper($_GET[self::PARAM_ACTION_DEBUG]);
        }
        return '';
    }

    public function toString() {
        $params = array();
        foreach($this->request as $key => $value) {
            $params .= ($params != '' ? ", " : '')."$key=$value";
        }
        return $params;
    }

    public function getRecognizedClickParameters() {
        $params = 'Debug='.$this->getDebug();
        $params .= ',Data1='.$this->getClickData1();
        $params .= ',Data2='.$this->getClickData2();

        return $params;
    }

    static public function getAffiliateClickParamName() {
        return Gpf_Settings::get(Pap_Settings::PARAM_NAME_USER_ID);
    }

    static public function getBannerClickParamName() {
        $parameterName = trim(Gpf_Settings::get(Pap_Settings::PARAM_NAME_BANNER_ID));
        if($parameterName == '') {
            $mesage = Gpf_Lang::_('Banner ID parameter name is empty. Review URL parameter name settings');
            Gpf_Log::critical($mesage);
            throw new Gpf_Exception($mesage);
        }
        return $parameterName;
    }

    static public function getChannelParamName() {
        return Pap_Tracking_Request::PARAM_CHANNEL_DEFAULT;
    }

    public function getChannelId() {
        return $this->getRequestParameter(self::getChannelParamName());
    }

    static public function getForcedAffiliateParamName() {
        return Pap_Tracking_Request::PARAM_FORCED_AFFILIATE_ID;
    }

    static public function getForcedBannerParamName() {
        return Pap_Tracking_Request::PARAM_FORCED_BANNER_ID;
    }

    public function getForcedCampaignId() {
        return $this->getRequestParameter(self::getForcedCampaignParamName());
    }

    static public function getForcedCampaignParamName() {
        return Pap_Tracking_Request::PARAM_FORCED_CAMPAIGN_ID;
    }

    public function getForcedChannelId() {
        return $this->getRequestParameter(Pap_Tracking_Request::PARAM_FORCED_CHANNEL_ID);
    }

    public function getCampaignId() {
        return $this->getRequestParameter(self::getCampaignParamName());
    }

    static public function getCampaignParamName() {
        $parameterName = trim(Gpf_Settings::get(Pap_Settings::PARAM_NAME_CAMPAIGN_ID));
        if($parameterName == '') {
            $mesage = Gpf_Lang::_('Campaign ID parameter name is empty. Review URL parameter name settings');
            Gpf_Log::critical($mesage);
            throw new Gpf_Exception($mesage);
        }
        return $parameterName;
    }

    public function getCurrency() {
        return $this->getRequestParameter(self::PARAM_CURRENCY);
    }

    /**
     * @deprecated used in CallBackTracker plugins only. should be moved to callback tracker
     */
    public function getPostParam($name) {
        if (!isset($_POST[$name])) {
            return '';
        }
        return $_POST[$name];
    }

    /**
     * This function does escape http:// and https:// in url as mod_rewrite disables requests with ://
     *
     * @param $url
     * @return encoded url
     */
    public static function encodeRefererUrl($url) {
        $url = str_replace('http://', 'H_', $url);
        $url = str_replace('https://', 'S_', $url);
        return $url;
    }

    /**
     * This function does decoded encoded url
     *
     * @param encoded $url
     * @return $url
     */
    public static function decodeRefererUrl($url) {
        if (substr($url, 0, 2) == 'H_') {
            return 'http://' . substr($url, 2);
        }
        if (substr($url, 0, 2) == 'S_') {
            return 'https://' . substr($url, 2);
        }
        return $url;
    }

    private function getForcedIp() {
        return $this->getRequestParameter(self::PARAM_FORCED_IP);
    }
}
?>
