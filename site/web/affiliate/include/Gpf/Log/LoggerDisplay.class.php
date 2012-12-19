<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LoggerDisplay.class.php 32103 2011-04-14 12:34:29Z jsimon $
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
class Gpf_Log_LoggerDisplay extends Gpf_Log_LoggerBase {
    const TYPE = 'display';

    private $lineFormat = "{TYPE}{GROUP} | {LEVEL} | {TIME} | {MESSAGE} | {IP} | {FILE} | {LINE}<br/>\n";
    private $timeFormat = "%Y-%m-%d %H:%M:%S";
    private $isHtml = true;

    public function __construct() {
        parent::__construct(self::TYPE);
    }

    public function setTimeFormat($format) {
        $this->timeFormat = $format;
    }

    public function setHtml($isHtml) {
        $this->isHtml = $isHtml;
    }

    public function setLineFormat($format) {
        $this->lineFormat = $format;
    }

    protected function log($time, $message, $logLevel, $logGroup, $ip, $file, $line, $type = null) {
        if($message == "") {
            echo "<br/>";
            return;
        }
         
        if($this->isHtml) {
            $message = str_replace(' ', '&nbsp;', $message);
        }
         
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
        echo $str;
    }
}
?>
