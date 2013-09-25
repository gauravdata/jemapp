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
 */
class Pap_Api_Tracker extends Gpf_Object {

    /**
     * @var Gpf_Api_Session
     */
    private $session;
    private $trackingResponse;
    private $visitorId;
    private $accountId;
    /**
     * @var array<Pap_Tracking_Action_RequestActionObject>
     */
    private $sales = array();
    const VISITOR_COOKIE_NAME = 'PAPVisitorId';
    
    const NOT_LOADED_YET = '-1';
    /**
     * @var Gpf_Rpc_Data
     */
    private $affiliate = self::NOT_LOADED_YET;
    /**
     * @var Gpf_Rpc_Data
     */
    private $campaign = self::NOT_LOADED_YET;
    
    /**
     * This class requires correctly initialized merchant session
     *
     * @param Gpf_Api_Session $session
     */
    public function __construct(Gpf_Api_Session $session) {
        if($session->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            throw new Exception("This class can be used only by merchant!");
        }
        $this->session = $session;
        $this->visitorId = @$_COOKIE[self::VISITOR_COOKIE_NAME];
    }
    
    public function setVisitorId($visitorId) {
        $this->visitorId = $visitorId;
    }

    public function getVisitorId() {
        return $this->visitorId;
    }

    public function setAccountId($accountId) {
        $this->accountId = $accountId;
    }
    
    public function track() {
        $request = new Gpf_Net_Http_Request();
        $request->setUrl(str_replace('server.php', 'track.php', $this->session->getUrl()));
        $request->setMethod('POST');

		$this->setQueryParams($request);
        if ($this->session->getDebug()) {
            $request->addQueryParam('PDebug', 'Y');
        }
        
        $request->setUrl($request->getUrl() . $request->getQuery());
        $request->setBody("sale=".$this->getSaleParams());
        if ($this->session->getDebug()) {
            echo 'Tracking request: '.$request->getUrl()."<br>\n";
        }
        $response = $this->sendRequest($request);
        $this->trackingResponse = trim($response->getBody());
        if ($this->session->getDebug()) {
            echo 'Tracking response: '.$this->trackingResponse."<br>\n";
        }
        $this->parseResponse();
        $this->affiliate = self::NOT_LOADED_YET;
    }
    
    protected function setQueryParams(Gpf_Net_Http_Request $request) {
    	$request->addQueryParam('visitorId', $this->visitorId);
    	$request->addQueryParam('accountId', $this->accountId);
        $request->addQueryParam('url', Pap_Tracking_Request::encodeRefererUrl($this->getUrl()));
        $request->addQueryParam('referrer', Pap_Tracking_Request::encodeRefererUrl($this->getReferrerUrl()));
        $request->addQueryParam('tracking', '1');
        $request->addQueryParam('getParams', $this->getGetParams()->getQuery());
        $request->addQueryParam('cookies', $this->getOldCookies());
        $request->addQueryParam('ip', $this->getIp());
        $request->addQueryParam('useragent', $this->getUserAgent());
    }
    
    protected function getIp() {
    	return @$_SERVER['REMOTE_ADDR'];
    }
    
    protected function getUserAgent() {
    	return @$_SERVER['HTTP_USER_AGENT'];
    }
    
    protected function sendRequest(Gpf_Net_Http_Request $request) {
        $client = new Gpf_Net_Http_Client();
        return $client->execute($request);
    }

    public function saveCookies() {
        if ($this->trackingResponse == '') {
            return;
        }
        $this->includeJavascript();
        $this->saveCookiesByJavascript();
    }

    public function save3rdPartyCookiesOnly($cookieDomainValidity = null) {
    	if ($this->visitorId == null) {
            return;
        }
        $this->save3rdPartyCookie(self::VISITOR_COOKIE_NAME, $this->visitorId, time() + 315569260, true, $cookieDomainValidity);
    }

    /**
     * @return Gpf_Rpc_Data
     */
    public function getAffiliate() {
    	return $this->getData($this->affiliate, 'getAffiliate', 'userid');
    }
    
    /**
     * @return Gpf_Rpc_Data
     */
    public function getCampaign() {
    	return $this->getData($this->campaign, 'getCampaign', 'campaignid');
    }
    
    private function getData(&$data, $method, $primaryKeyName) {
    	if ($this->visitorId == '') {
            return null;
        }
        if ($data === self::NOT_LOADED_YET) {
            $request = new Gpf_Rpc_DataRequest('Pap_Tracking_Visit_SingleVisitorProcessor', $method, $this->session);
            $request->addParam('visitorId', $this->visitorId);
            $request->addParam('accountId', $this->accountId);
            $request->sendNow();
            $data = $request->getData();
            if (is_null($data->getValue($primaryKeyName))) {
            	$data = null;
            }
        }
        return $data;
    }
    
    /**
     * Creates and returns new sale
     *
     * @return Pap_Tracking_ActionObject
     */
    public function createSale() {
        return $this->createAction('');
    }

    /**
     * Creates and returns new action
     *
     * @param string $actionCode
     * @return Pap_Tracking_ActionObject
     */
    public function createAction($actionCode = '') {
        $sale = new Pap_Tracking_Action_RequestActionObject();
        $sale->setActionCode($actionCode);
        $this->sales[] = $sale;
        return $sale;
    }

    protected function getSaleParams() {
        if (count($this->sales) == 0) {
            return '';
        }
        $json = new Gpf_Rpc_Json();
        return $json->encode($this->sales);
    }
    
    /**
     * Parses track.php response. Response can be empty or setVisitor('4c5e2151b8856e55dbfeb247c22300Hg');
     */
    private function parseResponse() {
        if ($this->trackingResponse == '') {
            return;
        }
        if (!preg_match('/^setVisitor\(\'([a-zA-Z0-9]+)\'\);/', $this->trackingResponse, $matches)) {
            return;
        }
        if ($matches[1] != '') {
            $this->visitorId = $matches[1];
        }
    }

    private function includeJavascript() {
        $trackjsUrl = str_replace('server.php', 'trackjs.php', $this->session->getUrl());
        echo '<script id="pap_x2s6df8d" src="'.$trackjsUrl.'" type="text/javascript"></script>';
    }

    private function saveCookiesByJavascript() {
        echo '<script type="text/javascript">'.$this->trackingResponse.'</script>';
    }

    protected function getUrl() {
        if (array_key_exists('PATH_INFO', $_SERVER) && @$_SERVER['PATH_INFO'] != '') {
            $scriptName = str_replace('\\', '/', @$_SERVER['PATH_INFO']);
        } else {
            if (array_key_exists('SCRIPT_NAME', $_SERVER)) {
                $scriptName = str_replace('\\', '/', @$_SERVER['SCRIPT_NAME']);
            } else {
                $scriptName = '';
            }
        }
        $portString = '';
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80
        && $_SERVER['SERVER_PORT'] != 443) {
            $portString = ':' . $_SERVER["SERVER_PORT"];
        }
        $protocol = 'http';
        if(isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0 && strtolower($_SERVER['HTTPS']) != 'off') {
            $protocol = 'https';
        }
        return $protocol . '://' . $this->getServerName() . $portString . $scriptName;
    }

    private function getServerName() {
        if (isset($_SERVER["SERVER_NAME"])) {
            return $_SERVER["SERVER_NAME"];
        }
        return 'localhost';
    }

    protected function getReferrerUrl() {
        if (array_key_exists('HTTP_REFERER', $_SERVER) && $_SERVER['HTTP_REFERER'] != '') {
            return $_SERVER['HTTP_REFERER'];
        }
        return '';
    }

    protected function getOldCookies() {
        $oldCookieNames = array('PAPCookie_Sale', 'PAPCookie_FirstClick', 'PAPCookie_LastClick');
        $oldCookies = '';
        foreach ($oldCookieNames as $oldCookieName) {
            if (array_key_exists($oldCookieName, $_COOKIE) && $_COOKIE[$oldCookieName] != '') {
                $oldCookies .= $oldCookieName.'='.urlencode($_COOKIE[$oldCookieName]).'||';
            }
        }
        return rtrim($oldCookies, '||');
    }

    /**
     * @return Gpf_Net_Http_Request
     */
    protected function getGetParams() {
        $getParams = new Gpf_Net_Http_Request();
        if (is_array($_GET) && count($_GET) > 0) {
            foreach ($_GET as $name => $value) {
                $getParams->addQueryParam($name, $value);
            }
        }
        return $getParams;
    }

    protected function save3rdPartyCookie($name, $value, $expire, $overwrite, $cookieDomainValidity = null) {
        if (!$overwrite && isset($_COOKIE[$name]) && $_COOKIE[$name] != '') {
            return;
        }
        if ($cookieDomainValidity == null) {
            Gpf_Http::setCookie($name, $value, $expire, "/");
        } else {
            Gpf_Http::setCookie($name, $value, $expire, "/", $cookieDomainValidity);
        }
    }

}
?>
