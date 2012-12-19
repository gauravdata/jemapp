<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Http.class.php 30392 2010-12-13 12:18:11Z mkendera $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Http extends Gpf_Object implements Gpf_HttpResponse {
    /**
     *
     * @var Gpf_HttpResponse
     */
    private static $instance = null;
    
    /**
     * @return Gpf_Http
     */
    private static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new Gpf_Http();
        }
        return self::$instance;
    }
    
    public static function setInstance(Gpf_HttpResponse $instance) {
        self::$instance = $instance;
    }
    
    public static function setCookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null) {
        self::getInstance()->setCookieValue($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
    
    public static function setHeader($name, $value, $httpResponseCode = null) {
        self::getInstance()->setHeaderValue($name, $value, true, $httpResponseCode);
    }
    
    public function setHeaderValue($name, $value, $replace = true, $httpResponseCode = null) {
        $fileName = '';
        $line = '';
        if(headers_sent($fileName, $line)) {
            throw new Gpf_Exception("Headers already sent in $fileName line $line while setting header $name: $value");
        }
        header($name . ': ' . $value, $replace, $httpResponseCode);
    }
    
    public function setCookieValue($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
    
    public static function getCookie($name) {
        if (!array_key_exists($name, $_COOKIE)) {
            return null;
        }
        return $_COOKIE[$name];
    }
    
    public static function getRemoteIp() {
        $ip = '';
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        $ipAddresses = explode(',', $ip);   //HTTP_X_FORWARDED_FOR returns multiple IP addresses
        return trim($ipAddresses[0]);
    }
}
?>
