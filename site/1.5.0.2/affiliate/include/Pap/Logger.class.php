<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * Class for constants that are used by multiple objects or tables
 *
 * @package PostAffiliatePro
 */
class Pap_Logger  {
    
    const SYSTEM_DEBUG_TYPE = 'O';
    
    private static function checkActionTypeInDebugTypes($type) {
        $debugTypes = Gpf_Settings::get(Pap_Settings::DEBUG_TYPES);
        if($debugTypes == '') {
            return false;
        }
         
        $arr = explode(",", $debugTypes);
        if(in_array($type, $arr)) {
            return true;
        }
        return false;
    }
    
    public static function getLogLevel($type = self::SYSTEM_DEBUG_TYPE) {
        $logLevel = Gpf_Settings::get(Gpf_Settings_Gpf::LOG_LEVEL_SETTING_NAME);
                  
        if(self::checkActionTypeInDebugTypes($type)) {
            $logLevel = Gpf_Log::DEBUG;
        }
        
        return $logLevel;
    }

    /**
     * @param $type
     * @return Gpf_Log_Logger
     */
    public static function create($type = self::SYSTEM_DEBUG_TYPE) {
        $logLevel = self::getLogLevel($type);
        
        $request = new Pap_Tracking_Request();
        if($request->getDebug() == Gpf::YES) {
            $logLevel = Gpf_Log::DEBUG;
        }
        
        $logger = Gpf_Log_Logger::getInstance($type);
        $logger->setGroup(substr($type, 0, 4) . '-' . Gpf_Common_String::generateId(10));
        $logger->setType($type);
        $logger->add(Gpf_Log_LoggerDatabase::TYPE, $logLevel);

        if($request->getDebug() == Gpf::YES) {
            $logger->add(Gpf_Log_LoggerDisplay::TYPE, $logLevel);
        }
         
        return $logger;
    }
}
?>
