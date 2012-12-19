<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Context.class.php 26474 2009-12-08 15:08:45Z mbebjak $
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
class Gpf_Plugins_Context {
	
	/**
	 * array of context parameters
	 *
	 * @var unknown_type
	 */
	private $_parameters = array();
    
    /**
     * @var Gpf_Log_Logger
     */
    private $_logger = null;

    protected function __construct() {
    }

    /**
     * @return Gpf_Log_Logger
     */
    public function getLogger() {
        return $this->_logger;
    }
    
    public function getLoggerGroupId() {
        if ($this->_logger != null) {
            return $this->_logger->getGroup();
        }
        return null;
    }

    public function setLogger($logger) {
        $this->_logger = $logger;
    }

    /**
     * returns array with all keys (parameter names) that are currently
     * stored in the context
     *
     * @return unknown
     */
    public function getAllKeys() {
        $keys = array();
        foreach($this->_parameters as $key) {
            $keys[] = $key;
        }

        return $keys;
    }

    public function set($key, $value) {
        $this->_parameters[$key] = $value;
    }

    public function get($key) {
        if(!isset($this->_parameters[$key])) {
            return null;
        }
        return $this->_parameters[$key];
    }

    public function log($logLevel, $message, $logGroup = null) {
        if($this->_logger !== null) {
            $this->_logger->log($message, $logLevel, $logGroup);
        }
    }

    public function critical($msg) {
        $this->log(Gpf_Log::CRITICAL, $msg);
    }

    public function debug($msg) {
        $this->log(Gpf_Log::DEBUG, $msg);
    }

    public function info($msg) {
        $this->log(Gpf_Log::INFO, $msg);
    }

    public function error($msg) {
        $this->log(Gpf_Log::ERROR, $msg);
    }

    public function warning($msg) {
        $this->log(Gpf_Log::WARNING, $msg);
    }
}
?>
