<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: CachedResponse.class.php 23149 2009-01-16 16:20:37Z mbebjak $
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
class Gpf_Rpc_CachedResponse extends Gpf_Object {
    private $responses = array();
    private $encodedResponses = array();
    /**
     * @var Gpf_Rpc_CachedResponse
     */
    private static $instance = null;
    
    private function __construct() {
        
    }
    
    /**
     * @return Gpf_Rpc_CachedResponse
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new Gpf_Rpc_CachedResponse();
        }
        return self::$instance;
    }
    
    public static function reset() {
        $instance = self::getInstance();
        $instance->clearResponse();
    }
    
    public static function add(Gpf_Rpc_Serializable $response, $className, $methodName, $id = '') {
        $instance = self::getInstance();
        $instance->addResponse($response, $className, $methodName, $id);
    }
    
    public static function addById(Gpf_Rpc_Serializable $response, $id) {
        $instance = self::getInstance();
        $instance->addResponse($response, '', '', $id);
    }
    
    public static function addEncodedById($encodedValue, $id) {
        $instance = self::getInstance();
        $instance->addEncodedResponse($encodedValue, '', '', $id);
    }
    
    private function addEncodedResponse($encodedValue, $className, $methodName, $id = '') {
        $this->encodedResponses[$this->getKey($className, $methodName, $id)] = trim($encodedValue);
    }
    
    private function addResponse(Gpf_Rpc_Serializable $response, $className, $methodName, $id = '') {
        $this->responses[$this->getKey($className, $methodName, $id)] = $response;
    }
    
    private function getKey($className, $methodName, $id) {
        return md5(strtolower($className) . '|' . $methodName . '|' . $id);
    }
    
    private function clearResponse() {
        $this->responses = array();
    }
    
    public static function render() {
        $encoder = new Gpf_Rpc_Json();
        $out = '';
        $response = self::getInstance();
        
        foreach ($response->encodedResponses as $id => $value) {
            $out .= 'window["' . $id . '"]="' . $value . '";';                
        }
        
        foreach ($response->responses as $id => $value) {
            $out .= 'window["' . $id . '"]="' . addcslashes($encoder->encodeResponse($value), '"\\') . '";';                
        }
        return '<script type="text/javascript">' . $out . '</script>';
    }
}
?>
