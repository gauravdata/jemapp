<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DateTime.class.php 33005 2011-06-02 14:41:48Z mbebjak $
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
class Gpf_DateTime extends Gpf_Object {
    
    // 1.1.1970 00:00:00
    const MIN_TIMESTAMP = 0;
    // 31.12.2030 14:59:59
    const MAX_TIMESTAMP = 1924988399;
    
    private $timestamp;
    /**
     * @var boolean
     */
    private $serverTime;

    /**
     * Creates date object
     *
     * @param $time null, timestamp or datetime in text format. if no parameter is specified current time is used
     */
    public function __construct($time = null, $serverTime = true) {
         $this->init($time);
         $this->serverTime = $serverTime;
    }
    
    public static function daysToSeconds($days) {
        return $days*24*60*60;
    }
    
    public static function hoursToSeconds($hours) {
        return $hours*60*60;
    }
    
    public static function minutesToSeconds($minutes) {
        return $minutes*60;
    }
    
    public static function weeksToSeconds($weeks) {
        return $weeks*7*24*60*60;
    }
    
    public static function monthsToSeconds($months) {
        return $months*30*24*60*60;
    }
    
    public static function yearsToSeconds($years) {
        return $years*365*24*60*60;
    }

    public static function secondsToHours($seconds) {
        return $seconds/60/60;
    }
    
    /**
     * @return Gpf_DateTime
     */
    public function makeClone() {
        return new Gpf_DateTime($this->timestamp, $this->serverTime);
    }

    /**
     * @return Gpf_DateTime
     */
    public function getServerTime() {
        if ($this->serverTime) {
            return $this;
        }
        return new Gpf_DateTime(Gpf_Common_DateUtils::getServerTime($this->timestamp), true);
    }
        
    /**
     * @return Gpf_DateTime
     */
    public function getClientTime() {
        if ($this->serverTime) {
            return new Gpf_DateTime(Gpf_Common_DateUtils::getClientTime($this->timestamp), false);
        }
        return $this;
    }
    
    public function toTimeStamp() {
        return $this->timestamp;
    }
    
    /**
     * @return date string in system format
     */
    public function toDate() {
        return Gpf_Common_DateUtils::getOnlyDatePart($this->toDateTime());
    }
    
    /**
     * @return date in locale format
     */
    public function toLocaleDate() {
        return Gpf_Common_DateUtils::getDateInLocaleFormat($this->timestamp);
    }
    
    public function format($format) {
        return date($format, $this->timestamp);
    }
    
    /**
     * @return datetime string in system format
     */
    public function toDateTime() {
        return Gpf_Common_DateUtils::getDateTime($this->timestamp);
    }

    /**
     * @return Gpf_DateTime start of the month
     */
    public function getMonthStart() {
        return new Gpf_DateTime(mktime(0, 0, 0, $this->getMonth(), 1, $this->getYear()), $this->serverTime);
    }
    
    /**
     * @return Gpf_DateTime end of the month
     */
    public function getMonthEnd() {
        return new Gpf_DateTime(mktime(23, 59, 59,
                                   $this->getMonth(),
                                   Gpf_Common_DateUtils::getDaysInMonth($this->getMonth(), $this->getYear()),
                                   $this->getYear()), $this->serverTime);
    }
    
    /**
     * @return Gpf_DateTime start of the week
     */
    public function getWeekStart() {
        return new Gpf_DateTime(mktime(0, 0, 0,
                                $this->getMonth(),
                                $this->getDay() - date('w', $this->timestamp),
                                $this->getYear()), $this->serverTime);
    }
    
    /**
     * @return Gpf_DateTime end of the week
     */
    public function getWeekEnd() {
        return new Gpf_DateTime(mktime(23, 59, 59,
                                $this->getMonth(),
                                $this->getDay() - date('w', $this->timestamp) + 6,
                                $this->getYear()), $this->serverTime);
    }
    
    /**
     * @return Gpf_DateTime start of the day
     */
    public function getDayStart() {
        return new Gpf_DateTime(mktime(0, 0, 0,
                                $this->getMonth(),
                                $this->getDay(),
                                $this->getYear()), $this->serverTime);
    }
    
    /**
     * @return Gpf_DateTime start of the day
     */
    public function getHourStart() {
        return new Gpf_DateTime(mktime($this->getHour(), 0, 0,
                                $this->getMonth(),
                                $this->getDay(),
                                $this->getYear()), $this->serverTime);
    }
    
    /**
     * @return Gpf_DateTime end of the day
     */
    public function getDayEnd() {
        return new Gpf_DateTime(mktime(23, 59, 59,
                                $this->getMonth(),
                                $this->getDay(),
                                $this->getYear()), $this->serverTime);
    }
    
    /**
     * @param $date
     * @return 0 if times are equal, -1 if $this is before $date, 1 if $this is after $date
     */
    public function compare(Gpf_DateTime $date) {
        if ($this->timestamp == $date->timestamp) {
            return 0;
        }
        if ($this->timestamp < $date->timestamp) {
            return -1;
        }
        return 1;
    }
    
    /**
     * @return year (numeric 4 digits)
     */
    public function getYear() {
        return date('Y', $this->timestamp);
    }
    
    /**
     * @return month (numeric with leading zeros)
     */
    public function getMonth() {
        return date('m', $this->timestamp);
    }
    
    /**
     * @return week (numeric with leading zeros)
     */
    public function getWeek() {
        return date('W', $this->timestamp);
    }
    
    /**
     * @return day (numeric with leading zeros)
     */
    public function getDay() {
        return date('d', $this->timestamp);
    }
    
    /**
     * @return hour (numeric with leading zeros)
     */
    public function getHour() {
        return date('H', $this->timestamp);
    }
    
    /**
     * @return minute (numeric with leading zeros)
     */
    public function getMinute() {
        return date('i', $this->timestamp);
    }
    
    /**
     * @return second (numeric with leading zeros)
     */
    public function getSecond() {
        return date('s', $this->timestamp);
    }
    
    public function checkTimestamp($timestamp) {
        if ($timestamp < self::MIN_TIMESTAMP) {
            $this->timestamp = self::MIN_TIMESTAMP;
            return false;
        }
        
        if ($timestamp > self::MAX_TIMESTAMP) {
            $this->timestamp = self::MAX_TIMESTAMP;
            return false;
        }
        return true;
    }
    
    public function addDay($count = 1) {
        if (!$this->checkTimestamp($this->toTimeStamp() + self::daysToSeconds($count))) {
            return;
        }
        $this->timestamp = $this->maketime($this->getMonth(), $this->getDay()+$count, $this->getYear());
    }
    
    public function addSecond($count = 1) {
        if (!$this->checkTimestamp($this->toTimeStamp() + $count)) {
            return;
        }
        $this->timestamp = mktime($this->getHour(), $this->getMinute(), $this->getSecond()+$count, $this->getMonth(), $this->getDay(), $this->getYear());
    }
    
    public function addMinute($count = 1) {
        if (!$this->checkTimestamp($this->toTimeStamp() + self::minutesToSeconds($count))) {
            return;
        }
        $this->timestamp = mktime($this->getHour(), $this->getMinute()+$count, $this->getSecond(), $this->getMonth(), $this->getDay(), $this->getYear());
    }
    
    public function addHour($count = 1) {
        if (!$this->checkTimestamp($this->toTimeStamp() + self::hoursToSeconds($count))) {
            return;
        }
        $this->timestamp = mktime($this->getHour()+$count, $this->getMinute(), $this->getSecond(), $this->getMonth(), $this->getDay(), $this->getYear());
    }
    
    public function addWeek($count = 1) {
        if (!$this->checkTimestamp($this->toTimeStamp() + self::weeksToSeconds($count))) {
            return;
        }
        $this->timestamp = $this->maketime($this->getMonth(), $this->getDay()+$count*7, $this->getYear());
    }
    
    public function addMonth($count = 1) {
        if (!$this->checkTimestamp($this->toTimeStamp() + self::monthsToSeconds($count))) {
            return;
        }
        $month = $this->getMonth()+$count;
        $day = Gpf_Common_DateUtils::daysInMonth($month, $this->getDay(), $this->getYear());
        $this->timestamp = $this->maketime($month, $day, $this->getYear());
    }
    
    /**
     *
     * @return Gpf_DateTime
     */
    public static function min() {
        return new Gpf_DateTime(self::MIN_TIMESTAMP);
    }
    
    private function maketime($month, $day, $year) {
        return mktime($this->getHour(), $this->getMinute(), $this->getSecond(), $month, $day, $year);
    }
    
    private function init($time = null) {
        if ($time === null) {
            $this->timestamp = time();
            return;
        }
        if (is_int($time)) {
            $this->timestamp = $time;
            return;
        }
        $this->timestamp = Gpf_Common_DateUtils::getTimestamp($time);
    }
}
?>
