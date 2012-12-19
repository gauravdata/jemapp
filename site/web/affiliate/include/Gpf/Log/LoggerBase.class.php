<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LoggerBase.class.php 26473 2009-12-08 15:05:10Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * General purpose logger
 *
 */
/**
 * @package GwtPhpFramework
 */
abstract class Gpf_Log_LoggerBase extends Gpf_Object {
	private $logLevel = Gpf_Log::ERROR;
	private $type = '';
	
	public function __construct($type) {
	    $this->type = $type;
	}
	
    public function setLogLevel($level) {
    	$this->logLevel = $level;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getLogLevel() {
    	return $this->logLevel;
    }
    
    public function logMessage($time, $message, $logLevel, $logGroup, $ip, $file, $line, $type = null) {
        if($logLevel < $this->getLogLevel()) {
            return;
        }
        $this->log($time, $message, $logLevel, $logGroup, $ip, $file, $line, $type);
    }
    
    abstract protected function log($time, $message, $logLevel, $logGroup, $ip, $file, $line, $type = null);
    
    /**
     * return name of log level as text
     *
     * @param const int $logLevel
     * @return string
     */
    protected function getLogLevelAsText($logLevel) {
    	switch($logLevel) {
    		case Gpf_Log::CRITICAL: return 'Critical';
    		case Gpf_Log::ERROR:    return 'Error';
    		case Gpf_Log::WARNING:  return 'Warning';
    		case Gpf_Log::INFO:     return 'Info';
    		case Gpf_Log::DEBUG:    return 'Debug';
    	}
    	
    	return ' Unknown';
    }
}
?>
