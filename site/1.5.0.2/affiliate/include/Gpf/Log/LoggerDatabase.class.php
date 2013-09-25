<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LoggerDatabase.class.php 26473 2009-12-08 15:05:10Z mbebjak $
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
class Gpf_Log_LoggerDatabase extends Gpf_Log_LoggerBase {
    const TYPE = 'database';

    public function __construct() {
        parent::__construct(self::TYPE);
    }

    protected function log($time, $message, $logLevel, $logGroup, $ip, $file, $line, $type = null) {
        if($message == "") {
            return;
        }
        $timeString = strftime("Y-m-d H:i:s", $time);
         
        $userId = null;
		try {
        	$userId = Gpf_Session::getAuthUser()->getUserId();
		} catch(Gpf_Exception $e) {	}
        
        try {
            $dbLog = new Gpf_Db_Log();
            $dbLog->set('groupid', $logGroup);
            $dbLog->set('level', $logLevel);
            $dbLog->set('created', $timeString);
            $dbLog->set('filename', $file);
            $dbLog->set('message', $message);
            $dbLog->set('line', $line);
            $dbLog->set('ip', $ip);
            $dbLog->set('accountuserid', $userId);
            $dbLog->set(Gpf_Db_Table_Logs::TYPE, $type);
            $dbLog->save();
        } catch(Exception $e) {
            Gpf_Log::disableType(Gpf_Log_LoggerDatabase::TYPE);
            Gpf_Log::error($this->_sys("Database Logger Error. Logging on display: %s", $message));
            Gpf_Log::enableAllTypes();
        }
    }
}
?>
