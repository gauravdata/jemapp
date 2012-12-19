<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Logger.class.php 33487 2011-06-27 11:55:54Z mkendera $
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
 * @package GwtPhpFramework
 */
class Gpf_Log_Logger extends Gpf_Object {
    /**
     * @var array
     */
    static private $instances = array();
    /**
     * @var array
     */
    private $loggers = array();

    /**
     * array of custom parameters
     */
    private $customParameters = array();
    
    private $disabledTypes = array();
    
    private $group = null;
    private $type = null;
    private $logToDisplay = false;
    
    /**
     * returns instance of logger class.
     * You can add instance name, if you want to have multiple independent instances of logger
     *
     * @param string $instanceName
     * @return Gpf_Log_Logger
     */
    public static function getInstance($instanceName = '_') {
        if($instanceName == '') {
            $instanceName = '_';
        }

        if (!array_key_exists($instanceName, self::$instances)) {
            self::$instances[$instanceName] = new Gpf_Log_Logger();
        }
        $instance = self::$instances[$instanceName];
        return $instance;
    }
    
    public static function isLoggerInsert($sqlString) {
        return strpos($sqlString, 'INSERT INTO ' . Gpf_Db_Table_Logs::getName()) !== false;
    }
    
    /**
     * attachs new log system
     *
     * @param unknown_type $system
     * @return Gpf_Log_LoggerBase
     */
    public function add($type, $logLevel) {
    	if($type == Gpf_Log_LoggerDisplay::TYPE) {
    		$this->logToDisplay = true;
    	}
        return $this->addLogger($this->create($type), $logLevel);
    }

    /**
     * Checks if logger with te specified type was already initialized
     *
     * @param unknown_type $type
     * @return unknown
     */
    public function checkLoggerTypeExists($type) {
        if(array_key_exists($type, $this->loggers)) {
        	return true;
        }
    	
        return false;
    }
    
    /**
     * returns true if debugging writes log to display
     *
     * @return boolean
     */
    public function isLogToDisplay() {
    	return $this->logToDisplay;
    }
    
    public function removeAll() {
        $this->loggers = array();
        $this->customParameters = array();
        $this->disabledTypes = array();
        $this->group = null;
    }
    
    /**
     *
     * @param Gpf_Log_LoggerBase $logger
     * @param int $logLevel
     * @return Gpf_Log_LoggerBase
     */
    public function addLogger(Gpf_Log_LoggerBase $logger, $logLevel) {
        if(!$this->checkLoggerTypeExists($logger->getType())) {
        	$logger->setLogLevel($logLevel);
        	$this->loggers[$logger->getType()] = $logger;
        	return $logger;
        } else {
        	$ll = new Gpf_Log_LoggerDatabase();
        	$existingLogger = $this->loggers[$logger->getType()];
        	if($existingLogger->getLogLevel() > $logLevel) {
        		$existingLogger->setLogLevel($logLevel);
        	}
        	return $existingLogger;
        }
    }
    
    public function getGroup() {
        return $this->group;
    }
        
    public function setGroup($group = null) {
        $this->group = $group;
        if($group === null) {
            $this->group = Gpf_Common_String::generateId(10);
        }
    }
    
    public function setType($type) {
        $this->type = $type;
    }
    
    /**
     * function sets custom parameter for the logger
     *
     * @param string $name
     * @param string $value
     */
    public function setCustomParameter($name, $value) {
        $this->customParameters[$name] = $value;
    }

    /**
     * returns custom parameter
     *
     * @param string $name
     * @return string
     */
    public function getCustomParameter($name) {
        if(isset($this->customParameters[$name])) {
            return $this->customParameters[$name];
        }
        return '';
    }

    /**
     * logs message
     *
     * @param string $message
     * @param string $logLevel
     * @param string $logGroup
     */
    public function log($message, $logLevel, $logGroup = null) {
        $time = time();
        $group = $logGroup;
        if($this->group !== null) {
            $group = $this->group;
            if($logGroup !== null) {
                $group .= ' ' . $logGroup;
            }
        }
	
        $callingFile = $this->findLogFile();
        $file = $callingFile['file'];
        if(isset($callingFile['classVariables'])) {
        	$file .= ' '.$callingFile['classVariables'];
        }
        $line = $callingFile['line'];

        $ip = Gpf_Http::getRemoteIp();
        if ($ip = '') {
            $ip = '127.0.0.1';
        }

        foreach ($this->loggers as $logger) {
        	if(!in_array($logger->getType(), $this->disabledTypes)) {
                $logger->logMessage($time, $message, $logLevel, $group, $ip, $file, $line, $this->type);
            }
        }
    }
    
    /**
     * logs debug message
     *
     * @param string $message
     * @param string $logGroup
     */
    public function debug($message, $logGroup = null) {
        $this->log($message, Gpf_Log::DEBUG, $logGroup);
    }

    /**
     * logs info message
     *
     * @param string $message
     * @param string $logGroup
     */
    public function info($message, $logGroup = null) {
        $this->log($message, Gpf_Log::INFO, $logGroup);
    }

    /**
     * logs warning message
     *
     * @param string $message
     * @param string $logGroup
     */
    public function warning($message, $logGroup = null) {
        $this->log($message, Gpf_Log::WARNING, $logGroup);
    }

    /**
     * logs error message
     *
     * @param string $message
     * @param string $logGroup
     */
    public function error($message, $logGroup = null) {
        $this->log($message, Gpf_Log::ERROR, $logGroup);
    }

    /**
     * logs critical error message
     *
     * @param string $message
     * @param string $logGroup
     */
    public function critical($message, $logGroup = null) {
        $this->log($message, Gpf_Log::CRITICAL, $logGroup);
    }

    public function disableType($type) {
        $this->disabledTypes[$type] =$type;
    }
    
    public function enableAllTypes() {
        $this->disabledTypes = array();
    }
    
    /**
     *
     * @return Gpf_Log_LoggerBase
     */
    private function create($type) {
        switch($type) {
            case Gpf_Log_LoggerDisplay::TYPE:
                return new Gpf_Log_LoggerDisplay();
            case Gpf_Log_LoggerFile::TYPE:
                return new Gpf_Log_LoggerFile();
            case Gpf_Log_LoggerDatabase::TYPE:
            case 'db':
                return new Gpf_Log_LoggerDatabase();
        }
        throw new Gpf_Log_Exception("Log system '$type' does not exist");
    }
    
    private function findLogFile() {
        $calls = debug_backtrace();
        
        $foundObject = null;
        
        // special handling for sql benchmarks
        if($this->sqlBenchmarkFound($calls)) {
            $foundObject = $this->findFileBySqlBenchmark();
        }

        if($foundObject == null) {
            $foundObject = $this->findFileByCallingMethod($calls);
        }
        if($foundObject == null) {
            $foundObject = $this->findLatestObjectBeforeString("Logger.class.php");
        }
        if($foundObject == null) {
            $last = count($calls);
            $last -= 1;
            if($last <0) {
                $last = 0;
            }
        
            $foundObject = $calls[$last];
        }
        
        return $foundObject;
    }
    
    private function sqlBenchmarkFound($calls) {
        foreach($calls as $obj) {
            if(isset($obj['function']) && $obj['function'] == "sqlBenchmarkEnd") {
                return true;
            }
        }
        return false;
    }
    
    private function findFileBySqlBenchmark() {
        $foundFile = $this->findLatestObjectBeforeString("DbEngine");
        if($foundFile != null && is_object($foundFile['object'])) {
            $foundFile['classVariables'] = $this->getObjectVariables($foundFile['object']);
        }
        return $foundFile;
    }
    
    private function getObjectVariables($object) {
        if(is_object($object)) {
            $class = get_class($object);
            $methods = get_class_methods($class);
            if(in_array("__toString", $methods)) {
                return $object->__toString();
            }
        }
        return '';
    }
    
    private function findFileByCallingMethod($calls) {
        $functionNames = array('debug', 'info', 'warning', 'error', 'critical', 'log');
        $foundObject = null;
        foreach($functionNames as $name) {
            $foundObject = $this->findCallingFile($calls, $name);
            if($foundObject != null) {
                return $foundObject;
            }
        }
        
        return null;
    }
    
    private function findCallingFile($calls, $functionName) {
        foreach($calls as $obj) {
            if(isset($obj['function']) && $obj['function'] == $functionName) {
                return $obj;
            }
        }
        
        return null;
    }
    
    private function findLatestObjectBeforeString($text) {
        $callsReversed = array_reverse( debug_backtrace() );
    
        $lastObject = null;
        foreach($callsReversed as $obj) {
            if(!isset($obj['file'])) {
                continue;
            }
            $pos = strpos($obj['file'], $text);
            if($pos !== false && $lastObject != null) {
                return $lastObject;
            }
            $lastObject = $obj;
        }
        return null;
    }
}
?>
