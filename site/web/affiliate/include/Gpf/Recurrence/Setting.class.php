<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
abstract class Gpf_Recurrence_Setting extends Gpf_Object {

    protected $year;
    protected $month;
    protected $day;
    
    protected $hour;
    protected $minute;
    protected $second;
    
    protected $period;
    protected $frequency;
    
    /**
     * @var Gpf_Db_RecurrenceSetting
     */
    protected $recurrenceSetting;
    
    public function __construct(Gpf_Db_RecurrenceSetting $setting) {
        $this->period = $setting->getPeriod();
        $this->frequency = $setting->getFrequency();
    }
    
    protected function parseTimeStamp($timestamp) {
        if ($timestamp < Gpf_DateTime::MIN_TIMESTAMP) {
            $timestamp = Gpf_DateTime::MIN_TIMESTAMP;
        }
        if ($timestamp > Gpf_DateTime::MAX_TIMESTAMP) {
            $timestamp = Gpf_DateTime::MAX_TIMESTAMP;
        }
        $this->year  = date('Y', $timestamp);
        $this->month = date('n', $timestamp);
        $this->day   = date('j', $timestamp);
        
        $this->hour   = date('G', $timestamp);
        $this->minute = date('i', $timestamp);
        $this->second = date('s', $timestamp);
    }
    
    /**
     * @param timestamp $lastDate
     * @return next date timestamp or null if there is no other date
     */
    abstract public function getNextDate($lastDate);
}

?>
