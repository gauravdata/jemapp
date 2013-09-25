<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Cookie.class.php 29121 2010-08-24 08:24:42Z mbebjak $
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
class Pap_Tracking_Cookie extends Gpf_Object {
    const VISITOR_ID = "PAPVisitorId";

    const UNIQUE_IMPRESSION_COOKIE_NAME = "PAPCookie_Imp_";
    const SALE_COOKIE_NAME = 'PAPCookie_Sale';
    const FIRST_CLICK_COOKIE_NAME = 'PAPCookie_FirstClick';
    const LAST_CLICK_COOKIE_NAME = 'PAPCookie_LastClick';
    const TIME_COOKIE_NAME = 'PAPCookie_Time';

    const COOKIE_LIFETIME_FOREVER = 315569260;

    /**
     * @var string
     */
    private $cookieDomainValidity;
    private $javascriptDependentCookiesEnabled = true;
    private $javascriptCookies = "";
    /**
     * @var Gpf_Log_Logger
     */
    private $_logger = null;
    
    /**
     * @var Gpf_Data_RecordSet
     */
    private $savedCookies;

    private $cookiesArray;

    function __construct($cookiesArray = null) {
        $this->cookieDomainValidity = Gpf_Settings::get(Pap_Settings::COOKIE_DOMAIN);

        if ($cookiesArray != null) {
            $this->cookiesArray = $cookiesArray;
        } else {
            $this->cookiesArray = $_COOKIE;
        }

    }

    public static function generateNewVisitorId() {
        $stamp = microtime();
        $ip = $_SERVER['REMOTE_ADDR'];
        $id = md5($stamp*$ip + rand()) . crypt($ip + $stamp * rand(), CRYPT_BLOWFISH);

        $id = str_replace("$", "0", $id);
        $id = str_replace("/", "0", $id);
        $id = str_replace(".", "0", $id);
        $uniqueid = substr($id, rand(0, 13), Pap_Tracking_Visit_Processor::getVisitorIdLength());
        return $uniqueid;
    }

    public static function deleteOldCookies() {

    }

    public static function computeLifeTimeDaysToSeconds($days) {
        if ($days == 0) {
            return Pap_Tracking_Cookie::COOKIE_LIFETIME_FOREVER;
        }
        return $days * 24 * 60 * 60;
    }
    
    public function getLogger() {
        return $this->_logger;
    }

    public function setLogger($logger) {
        $this->_logger = $logger;
    }

    public function isLogToDisplay() {
        if($this->_logger != null) {
            return $this->_logger->isLogToDisplay();
        }
        return false;
    }

    public function log($logLevel, $message, $logGroup = null) {
        if($this->_logger != null) {
            $this->_logger->log($message, $logLevel, $logGroup);
        }
    }

    public function debug($msg) {
        $this->log(Gpf_Log::DEBUG, $msg);
    }

    public function getImpressionCookie($bannerId) {
        return $this->getCookie(self::UNIQUE_IMPRESSION_COOKIE_NAME.$bannerId);
    }
    
    public function getVisitorId() {
        return $this->getCookie(self::VISITOR_ID);
    }

    /**
     * @throws Pap_Tracking_Exception
     * @return Pap_Tracking_Cookie_Sale
     */
    public function getSaleCookie() {
        $cookie = new Pap_Tracking_Cookie_Sale();
        $cookieValue = $this->getCookie(self::SALE_COOKIE_NAME);
        if ($cookieValue == '') {
            throw new Pap_Tracking_Exception('Error occured while parsing sale cookie.');
        }
        $cookie->decode($cookieValue);
        return $cookie;
    }

    public function getRawSaleCookie() {
        return $this->getCookie(self::SALE_COOKIE_NAME);
    }

    public function getTimeCoookie() {
        return $this->getCookie(self::TIME_COOKIE_NAME);
    }

    public function setImpressionCookie($bannerId) {
        $this->set3rdPartyCookie(self::UNIQUE_IMPRESSION_COOKIE_NAME.$bannerId, time(), time() + self::COOKIE_LIFETIME_FOREVER);
    }

    public function setSaleCookie(Pap_Contexts_Click $context) {
        $userObject = $context->getUserObject();
        $campaignObject = $context->getCampaignObject();
        $channelValue = null;
        if($context->getChannelObject() != null) {
            $channelValue = $context->getChannelObject()->getId();
        }
    	
    	$campaignId = 0;
        $lifetime = 0;
        if($campaignObject != null) {
            $campaignId = $campaignObject->getId();
        } else {
            $this->debug("No campaign recognized");
        }
         
        $lifetime = time() + self::getCookieLifetime($context);
         
        $cookie = new Pap_Tracking_Cookie_Sale();
        $cookie->setAffiliateId($userObject->getId());
        $cookie->setCampaignId($campaignId);
        $cookie->setChannelId($channelValue);

        $this->setCookie(self::SALE_COOKIE_NAME,
                         $cookie,
                         $lifetime,
                         $this->isOverwriteEnabled($campaignObject, $userObject));
    }

    public function isOverwriteEnabled(Pap_Common_Campaign $campaignObject = null, Pap_Common_User $userObject = null) {
        if ($userObject != null) {
            try {
                switch (Gpf_Db_Table_UserAttributes::getSetting(Pap_Settings::OVERWRITE_COOKIE, $userObject->getAccountUserId())) {
                    case GPF::YES: return true;
                    case GPF::NO:  return false;
                    default: break;
                }
            } catch (Gpf_Exception $e) {
            }
        }
        $campaignOverwrite = 'D';
        if($campaignObject != null) {
            $campaignOverwrite = $campaignObject->getOverwriteCookie();
        }
        switch ($campaignOverwrite) {
            case GPF::YES: return true;
            case GPF::NO: return false;
            default: return $this->isGeneralOverwriteAllowed();
        }
    }

    private function isGeneralOverwriteAllowed() {
        $overwriteCookie = Gpf_Settings::get(Pap_Settings::OVERWRITE_COOKIE);
        if($overwriteCookie == Gpf::YES) {
            return true;
        }
        return false;
    }

    /**
     * @throws Pap_Tracking_Exception
     * @return Pap_Tracking_Cookie_ClickData
     */
    public function getFirstClickCookie() {
        $cookie = new Pap_Tracking_Cookie_ClickData();
        $cookieValue = $this->getCookie(self::FIRST_CLICK_COOKIE_NAME);
        if ($cookieValue == '') {
            throw new Pap_Tracking_Exception('Error occured while parsing first click cookie');
        }
        $cookie->decode($cookieValue);
        return $cookie;
    }

    public function setFirstClickCookie(Pap_Db_RawClick $click) {
        try {
            $actualValue = $this->getFirstClickCookie();
            $this->debug("Cookie value of first click is '$actualValue', we'll not overwrite it");
            return;
        } catch (Pap_Tracking_Exception $e) {
        }

        $this->debug("Saving first click data to cookie");
        $cookie = new Pap_Tracking_Cookie_ClickData();
        $cookie->setClick($click);

        $this->setCookie(self::FIRST_CLICK_COOKIE_NAME,
        $cookie,
        time() + self::COOKIE_LIFETIME_FOREVER,
        false);
    }

    /**
     * @throws Pap_Tracking_Exception
     * @return Pap_Tracking_Cookie_ClickData
     */
    public function getLastClickCookie() {
        $cookie = new Pap_Tracking_Cookie_ClickData();
        $cookieValue = $this->getCookie(self::LAST_CLICK_COOKIE_NAME);
        if ($cookieValue == '') {
            throw new Pap_Tracking_Exception('Error occured while parsing last click cookie');
        }
        $cookie->decode($cookieValue);
        return $cookie;
    }

    public function setLastClickCookie(Pap_Db_RawClick $click) {
        $cookie = new Pap_Tracking_Cookie_ClickData();
        $cookie->setClick($click);
        $this->setCookie(self::LAST_CLICK_COOKIE_NAME,
        $cookie,
        time() + self::COOKIE_LIFETIME_FOREVER,
        true);
    }

    public function setTimeCookie() {
        $count = $this->getTimeCoookie();
        if ($count == '') {
            $count = 1;
        } else {
            $count++;
        }
        $value = $count;
        $this->setCookie(self::TIME_COOKIE_NAME,
        $value,
        time() + self::COOKIE_LIFETIME_FOREVER,
        true);
    }

    public function setJavascriptDependentCookiesEnabled($enabled) {
        $this->javascriptDependentCookiesEnabled = $enabled;
    }

    /**
     * function will write output (JavaScript) that will save cookies in 1st party and Flash
     *
     */
    public function finishCookies() {
        echo $this->javascriptCookies;
    }

    private function getCookie($name) {
        if (!isset($this->cookiesArray[$name])) {
            return '';
        }

        return $this->cookiesArray[$name];
    }

    /**
     * Sets 3rd party, 1st party and flash cookie
     *
     * @param string $name
     * @param string $value
     * @param int $expire cookie lifetime in seconds
     * @param boolean $overwrite if an existing cookie should be overwritten
     */
    private function setCookie($name, $value, $expire, $overwrite) {
        $this->debug("Saving cookie '$name' = '$value', overwrite='$overwrite'");
        $this->debug("Cookie '$name' = '$value', expire=$expire saved with domain validity '".$this->cookieDomainValidity."'");
        
        if($this->isLogToDisplay()) {
            $this->debug("Log output is written to display, cookie is NOT saved!");
            return;
        }
         
        $currentValue = $this->getCookie($name);
        if ($currentValue == '' || $overwrite) {
            $this->set3rdPartyCookie($name, $value, $expire);
        } else {
            $this->debug("Cookie with name '$name' has value '$currentValue', we'll not overwrite it");
        }

        if ($this->javascriptDependentCookiesEnabled) {
            $dateExpire = date("Y-m-d", $expire);
            $this->javascriptCookies .= "_tracker.setCookie('$name', '$value', '$dateExpire', '".($overwrite ? '1' : '0')."');";
        }
        
        $this->addCookieToSavedCookiesList($name, $value, $expire, $overwrite);
    }
    
    private function addCookieToSavedCookiesList($name, $value, $expire, $overwrite) {
        $this->initSavedCookies();
        $this->savedCookies->add(array($name, $value, $expire, $overwrite ? Gpf::YES : Gpf::NO));
    }
    
    private function initSavedCookies() {
        if ($this->savedCookies != null) {
            return;
        }
        $this->savedCookies = new Gpf_Data_RecordSet();
        $this->savedCookies->setHeader(array('name', 'value', 'expire', 'overwrite'));
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    public function getSavedCookies() {
        $this->initSavedCookies();
        return $this->savedCookies;
    }

    private function set3rdPartyCookie($name, $value, $expire) {
        $this->sendP3PHeader();
        $this->debug("Cookie '$name' = '$value', expire=$expire saved with domain validity '".$this->cookieDomainValidity."'");
        if($this->cookieDomainValidity != '') {
            Gpf_Http::setCookie($name, $value, $expire, "/", $this->cookieDomainValidity);
        } else {
            Gpf_Http::setCookie($name, $value, $expire, "/");
        }
    }
    
    public static function getCookieLifetime(Pap_Contexts_Tracking $context) {
        if ($context->getCommissionGroup() !== null &&
        ($cookieLifetime = $context->getCommissionGroup()->getCookieLifetime()) > Pap_Db_CommissionGroup::COOKIE_LIFETIME_VALUE_SAME_AS_CAMPAIGN) {
        	return $cookieLifetime;
        }
        $lifetime = 0;
        if ($context->getCampaignObject() != null) {
            $lifetime = $context->getCampaignObject()->getCookieLifetime();
        }
        if($lifetime == 0) {
            $lifetime = self::COOKIE_LIFETIME_FOREVER;
        }
        return $lifetime;
    }
    
    public static function getCookieLifeTimeInDays(Pap_Contexts_Tracking $context) {
    	return self::getCookieLifetime($context) / 24 / 60 / 60;
    }
    
    private function sendP3PHeader() {
        $p3pPolicy = Gpf_Settings::get(Pap_Settings::URL_TO_P3P);
        $compactPolicy = Gpf_Settings::get(Pap_Settings::P3P_POLICY_COMPACT);

        if($p3pPolicy == '' && $compactPolicy == '') {
            return;
        }
        $p3pHeader = ($p3pPolicy == '' ? '' : 'policyref="'.$p3pPolicy.'"').
        ($compactPolicy != '' && $p3pPolicy != '' ? ', ' : '').($compactPolicy == '' ? '' : 'CP="'.$compactPolicy.'"');
         
        Gpf_Http::setHeader('P3P', $p3pHeader);
    }
}
?>
