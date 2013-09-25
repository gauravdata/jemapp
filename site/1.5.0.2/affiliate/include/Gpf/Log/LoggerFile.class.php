<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LoggerFile.class.php 26473 2009-12-08 15:05:10Z mbebjak $
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
class Gpf_Log_LoggerFile extends Gpf_Log_LoggerBase {
    const TYPE = 'file';

    private $lineFormat = "{TYPE}{GROUP} | {LEVEL} | {TIME} | {MESSAGE} | {IP} | {FILE} | {LINE}\r\n";
    private $timeFormat = "%b %d %H:%M:%S";
    private $fileName;
    
    public function __construct() {
        parent::__construct(self::TYPE);
    }
    
    public function setFileName($fileName) {
        $this->fileName = $fileName;
        $this->checkFileIsWritable();
    }
    
    protected function log($time, $message, $logLevel, $logGroup, $ip, $file, $line, $type = null) {
    	$timeString = strftime($this->timeFormat, $time);

    	$str = $this->lineFormat;
    	$str = str_replace('{GROUP}', $logGroup, $str);
    	$str = str_replace('{LEVEL}', $this->getLogLevelAsText($logLevel), $str);
    	$str = str_replace('{TIME}', $timeString, $str);
    	$str = str_replace('{MESSAGE}', $message, $str);
    	$str = str_replace('{IP}', $ip, $str);
    	$str = str_replace('{FILE}', $file, $str);
    	$str = str_replace('{LINE}', $line, $str);
    	$str = str_replace('{TYPE}', $type, $str);
    	
    	if($message == "") {
    		$str = " ";
    	}
    	
    	$file = new Gpf_Io_File($this->fileName);
    	try {
    	    $file->open('a');
    	    $file->write($str);
    	} catch (Exception $e) {
            throw new Gpf_Log_Exception("File logging error: " . $e->getMessage());
    	}
    }
    
    private function checkFileIsWritable() {
	    $file = new Gpf_Io_File($this->fileName);
	    $file->open('a');
	}
}
?>
