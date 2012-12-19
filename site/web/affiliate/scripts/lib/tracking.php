<?php
require_once 'lib/fast_init.php';

class Lib_VisitParams implements Lib_ConvertableToArray{
    private $visitorid = '';
    private $datevisit = '';
    private $url = '';
    private $referrerurl = '';
    private $trackmethod = '';
    private $get = '';
    private $anchor = '';
    private $sale = '';
    private $cookies = '';
    private $ip = '';
    private $useragent = '';
    private $accountid = '';
    private $visitoridhash = ''; 

    public function setVisitorId($visitorId) {
        $this->visitorid = $visitorId;
        $this->visitoridhash = $this->getVisitorIdHashFunctionForInsert($visitorId);
    }
    
    private function getVisitorIdHashFunctionForInsert($visitorId) {
        return sprintf('%u', crc32($visitorId)) % 255;
    }

    public function getVisitorId() {
        return $this->visitorid;
    }

    public function setDatevisit($datevisit) {
        $this->datevisit = $datevisit;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setReferrerUrl($referrerurl) {
        $this->referrerurl = $referrerurl;
    }

    public function setTrackMethod($trackmethod) {
        $this->trackmethod = $trackmethod;
    }

    public function getTrackMethod() {
        return $this->trackmethod;
    }

    public function setGet($get) {
        $this->get = $get;
    }

    public function setAnchor($anchor) {
        $this->anchor = $anchor;
    }

    public function setSale($sale) {
        $this->sale = $sale;
    }

    public function setCookies($cookies) {
        $this->cookies = $cookies;
    }

    public function setIp($ip) {
        $this->ip = $ip;
    }

    public function setUserAgent($useragent) {
        $this->useragent = $useragent;
    }

    public function setAccountId($accountid) {
        $this->accountid = $accountid;
    }

    public function getAccountId() {
        return $this->accountid;
    }

    public function __construct($params = array()) {
        $this->setDatevisit(date('Y-m-d H:i:s'));
    }

    public function toArray() {
        $variables = array();
        foreach ($this as $var => $value) {
            $variables[$var] = $value;
        }
        return $variables;
    }
}

class Lib_VisitorCookie {

    const VISITOR_COOKIE_NAME = 'PAPVisitorId';
    const VISITOR_PARAM_NAME = 'visitorId';
    const ACCOUNT_PARAM_NAME = 'accountId';
    const TRACK_METHOD_PARAM_NAME = 'tracking';
    const P3P_POLICY_COMPACT = 'p3p_policy_compact';
    const URL_TO_P3P = 'url_to_p3p';
    const COOKIE_DOMAIN = 'cookie_domain';

    const ACCOUNT_ID_LENGTH = 8;

    private $enableJs;
    private $allowDisplayJsNewVisitorId = false;

    /**
     * @var Lib_VisitParams
     */
    private $visitParams;

    public function __construct(Lib_VisitParams $visitParams, $enableJs = true) {
        $this->visitParams = $visitParams;
        $this->enableJs = $enableJs;
    }

    public static function readOldCookies($cookies = '') {
        if ($cookies != '') {
            return $cookies;
        }
        $oldCookies = '';
        foreach (array('PAPCookie_Sale', 'PAPCookie_FirstClick', 'PAPCookie_LastClick') as $oldCookieName) {
            if (@$_COOKIE[$oldCookieName] == '') {
                continue;
            }
            $oldCookies .= '||'.$oldCookieName.'='.$_COOKIE[$oldCookieName];
        }
        return $oldCookies;
    }

    public static function readVisitorIdAndAccountId(Lib_VisitParams $visitParams, Lib_SettingFile $settings, $enableJs = true) {
        $visitorCookie = new Lib_VisitorCookie($visitParams, $enableJs);
        $visitorCookie->processVisitorIdAndAccountIdToParams($settings);
    }

    public function processVisitorIdAndAccountIdToParams(Lib_SettingFile $settings) {
        $this->sendHeaders($settings);
        $this->recognizeVisitorParams();
        $this->setVisitorCookie($this->visitParams->getVisitorId(), $settings->get(self::COOKIE_DOMAIN));
        $this->displayJsNewVisitorId();
    }

    protected function sendJsCommand($command) {
        echo $command;
    }

    public function sendHeaders(Lib_SettingFile $settings) {
        $p3pPolicy = $settings->get(self::URL_TO_P3P);
        $compactPolicy = $settings->get(self::P3P_POLICY_COMPACT);

        if ($p3pPolicy == '' && $compactPolicy == '') {
            return;
        }

        $p3pHeader = ($p3pPolicy == '' ? '' : 'policyref="'.$p3pPolicy.'"').
        ($compactPolicy != '' && $p3pPolicy != '' ? ', ' : '').($compactPolicy == '' ? '' : 'CP="'.$compactPolicy.'"');

        $this->sendP3PHeader($p3pHeader);
    }

    protected function sendP3PHeader($value) {
        header('P3P: ' . $value, true);
    }

    protected function setVisitorCookie($visitorId, $cookieDomain = '') {
        if ($this->visitParams->getTrackMethod() === 'F') {
            return;
        }
        if ($cookieDomain != '') {
            @setcookie(self::VISITOR_COOKIE_NAME, $visitorId, time()+315360000, '/', $cookieDomain);
        } else {
            @setcookie(self::VISITOR_COOKIE_NAME, $visitorId, time()+315360000, '/');
        }
    }

    private function visitorIdParamExists() {
        return strlen($this->visitParams->getVisitorId()) == $this->getVisitorIdLength();
    }

    private function accountIdParamExists() {
        return strlen($this->visitParams->getAccountId()) == self::ACCOUNT_ID_LENGTH;
    }


    private function thirdPartyVisitorCookieExists() {
        return (@$_COOKIE[self::VISITOR_COOKIE_NAME] != '' && strlen(@$_COOKIE[self::VISITOR_COOKIE_NAME]) == $this->getVisitorIdLength());
    }


    private function recognizeVisitorParams() {
        if ($this->visitorIdParamExists() && !$this->accountIdParamExists()) {
            $this->setVisitorParams($this->visitParams->getVisitorId(), '', true);
            $this->allowDisplayJsNewVisitorId = false;
            return;
        }
        if ($this->visitorIdParamExists() && $this->accountIdParamExists()) {
            $this->setVisitorParams($this->visitParams->getVisitorId(),
            $this->visitParams->getAccountid(), true);
            $this->allowDisplayJsNewVisitorId = false;
            return;
        }
        if (!$this->visitorIdParamExists() && $this->accountIdParamExists()) {
            $this->setVisitorParams($this->getVisitorId(), $this->visitParams->getAccountId());
            $this->allowDisplayJsNewVisitorId = true;
            return;
        }
        $this->setVisitorParams($this->getVisitorId());
        $this->allowDisplayJsNewVisitorId = true;
    }

    protected function setVisitorParams($visitorId, $accountId = '', $isTrackingMethodSet = false) {
        $this->visitParams->setVisitorId($visitorId);
        $this->visitParams->setAccountid($accountId);

        if (!$isTrackingMethodSet) {
            $this->visitParams->setTrackMethod($this->getTrackingMethod());
        }
    }

    private function getVisitorId() {
        if ($this->thirdPartyVisitorCookieExists()) {
            return substr(@$_COOKIE[self::VISITOR_COOKIE_NAME], 0, $this->getVisitorIdLength());
        }
        return $this->generateNewId();
    }

    protected function displayJsNewVisitorId() {
        if ($this->enableJs && $this->allowDisplayJsNewVisitorId) {
            $this->sendJsCommand("setVisitor('".$this->visitParams->getVisitorId()."');\n");
        }
    }

    protected function getTrackingMethod() {
        if ($this->thirdPartyVisitorCookieExists()) {
            return '3';
        }
        return 'N';
    }

    private function getVisitorIdLength() {
        if (defined('VISITOR_ID_LENGTH')) {
            return VISITOR_ID_LENGTH;
        }
        return 32;
    }

    protected function generateNewId() {
        $stamp = microtime();
        $ip = @$_SERVER['REMOTE_ADDR'];
        $id = md5($stamp*$ip + rand()) . crypt($ip + $stamp * rand(), CRYPT_BLOWFISH);

        $id = str_replace("$", "0", $id);
        $id = str_replace("/", "0", $id);
        $id = str_replace(".", "0", $id);
        $uniqueid = substr($id, rand(0, 13), $this->getVisitorIdLength());
        return $uniqueid;
    }
}
?>
