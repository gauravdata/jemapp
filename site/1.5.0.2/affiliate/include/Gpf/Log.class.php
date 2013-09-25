<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Log.class.php 23735 2009-03-11 11:57:54Z mbebjak $
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
class Gpf_Log  {
    const CRITICAL = 50;
    const ERROR = 40;
    const WARNING = 30;
    const INFO = 20;
    const DEBUG = 10;
    
    /**
     * @var Gpf_Log_Logger
     */
    private static $logger;
       
    /**
     * @return Gpf_Log_Logger
     */
    private static function getLogger() {
        if (self::$logger == null) {
            self::$logger = Gpf_Log_Logger::getInstance();
        }
        return self::$logger;
    }
    
    private function __construct() {
    }
    
    public static function disableType($type) {
        self::getLogger()->disableType($type);
    }
    
    public static function enableAllTypes() {
        self::getLogger()->enableAllTypes();
    }
    
    /**
     * logs message
     *
     * @param string $message
     * @param string $logLevel
     * @param string $logGroup
     */
    public static function log($message, $logLevel, $logGroup = null) {
        self::getLogger()->log($message, $logLevel, $logGroup);
    }

    /**
     * logs debug message
     *
     * @param string $message
     * @param string $logGroup
     */
    public static function debug($message, $logGroup = null) {
        self::getLogger()->debug($message, $logGroup);
    }
        
    /**
     * logs info message
     *
     * @param string $message
     * @param string $logGroup
     */
    public static function info($message, $logGroup = null) {
        self::getLogger()->info($message, $logGroup);
    }
    
    /**
     * logs warning message
     *
     * @param string $message
     * @param string $logGroup
     */
    public static function warning($message, $logGroup = null) {
        self::getLogger()->warning($message, $logGroup);
    }
    
    /**
     * logs error message
     *
     * @param string $message
     * @param string $logGroup
     */
    public static function error($message, $logGroup = null) {
        self::getLogger()->error($message, $logGroup);
    }

    /**
     * logs critical error message
     *
     * @param string $message
     * @param string $logGroup
     */
    public static function critical($message, $logGroup = null) {
        self::getLogger()->critical($message, $logGroup);
    }

    /**
     * Attach new log system
     *
     * @param string $type 
     *      Gpf_Log_LoggerDisplay::TYPE
     *      Gpf_Log_LoggerFile::TYPE
     *      Gpf_Log_LoggerDatabase::TYPE
     * @param string $logLevel
     *      Gpf_Log::CRITICAL
     *      Gpf_Log::ERROR
     *      Gpf_Log::WARNING
     *      Gpf_Log::INFO
     *      Gpf_Log::DEBUG
     * @return Gpf_Log_LoggerBase
     */
    public static function addLogger($type, $logLevel) {
        if($type instanceof Gpf_Log_LoggerBase) {
            return self::getLogger()->addLogger($type, $logLevel);
        }
        return self::getLogger()->add($type, $logLevel);        
    }
    
    public static function removeAll() {
        self::getLogger()->removeAll();
    }
}
?>
