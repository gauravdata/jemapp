<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DateUtils.class.php 31902 2011-03-30 11:30:44Z mkendera $
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
class Gpf_Common_DateUtils extends Gpf_Object {
	const SECOND = "second";
    const DAY = "day";
    const WEEK = "week";
    const MONTH = "month";
    const YEAR = "year";
    const NONE = "";

    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    const DATE_FORMAT = 'Y-m-d';

    /**
     * returns true if year is the leap year
     *
     * @param $year
     * @return boolean
     */
    static public function isLeapYear($year) {
        return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0);
    }

    static private function belowMinTimestamp($timestamp) {
        if ($timestamp < Gpf_DateTime::MIN_TIMESTAMP) {
            return true;
        }
        return false;
    }
    
    static private function overMaxTimestamp($timestamp) {
        if ($timestamp > Gpf_DateTime::MAX_TIMESTAMP) {
            return true;
        }
        return false;
    }
    
    static public function addDateUnitToTimestamp($timestamp, $units, $unitType, $returnDate = true) {
        $d = $timestamp;

        switch($unitType) {
            case Gpf_Common_DateUtils::DAY:
                if (self::belowMinTimestamp($timestamp + Gpf_DateTime::daysToSeconds($units))) {
                    $time = Gpf_DateTime::MIN_TIMESTAMP;
                    break;
                }
                if (self::overMaxTimestamp($timestamp + Gpf_DateTime::daysToSeconds($units))) {
                    $time = Gpf_DateTime::MAX_TIMESTAMP;
                    break;
                }                
                $time = mktime(0, 0, 0, date("m", $d), date("d", $d)+$units, date("Y", $d));                           
                break;
            case Gpf_Common_DateUtils::WEEK:
                if (self::belowMinTimestamp($timestamp + Gpf_DateTime::weeksToSeconds($units))) {
                    $time = Gpf_DateTime::MIN_TIMESTAMP;
                    break;
                }
                if (self::overMaxTimestamp($timestamp + Gpf_DateTime::weeksToSeconds($units))) {
                    $time = Gpf_DateTime::MAX_TIMESTAMP;
                    break;
                }
                $time = mktime(0, 0, 0, date("m", $d), date("d", $d)+7*$units, date("Y", $d));
                break;
            case Gpf_Common_DateUtils::MONTH:
                if (self::belowMinTimestamp($timestamp + Gpf_DateTime::monthsToSeconds($units))) {
                    $time = Gpf_DateTime::MIN_TIMESTAMP;
                    break;
                }
                if (self::overMaxTimestamp($timestamp + Gpf_DateTime::monthsToSeconds($units))) {
                    $time = Gpf_DateTime::MAX_TIMESTAMP;
                    break;
                }
                $month = date("m", $d)+$units;
                $day = self::daysInMonth($month, date("d", $d), date("Y", $d));
                $time = mktime(0, 0, 0, $month, $day, date("Y", $d));
                break;
            case Gpf_Common_DateUtils::YEAR:
                if (self::belowMinTimestamp($timestamp + Gpf_DateTime::yearsToSeconds($units))) {
                    $time = Gpf_DateTime::MIN_TIMESTAMP;
                    break;
                }
                if (self::overMaxTimestamp($timestamp + Gpf_DateTime::yearsToSeconds($units))) {
                    $time = Gpf_DateTime::MAX_TIMESTAMP;
                    break;
                }
                $time = mktime(0, 0, 0, date("m", $d), date("d", $d), date("Y", $d)+$units);
                break;
        }
		if ($returnDate) {
        	return Gpf_Common_DateUtils::getDate($time);
		}
		return Gpf_Common_DateUtils::getDateTime($time);
    }

    static public function addDateUnit($date, $units, $unitType) {
        return self::addDateUnitToTimestamp(Gpf_Common_DateUtils::getTimestamp($date), $units, $unitType);
    }

    static public function getTimestamp($date) {
        return strtotime($date);
    }

    static public function getDate($timestamp) {
        return date(self::DATE_FORMAT, $timestamp);
    }

    static public function getDateTime($timestamp) {
        return date(self::DATETIME_FORMAT, $timestamp);
    }

    static public function formatByUnit(Gpf_DateTime $date, $unitType) {
        switch($unitType) {
            case Gpf_Common_DateUtils::DAY:
                return $date->toLocaleDate();
            case Gpf_Common_DateUtils::MONTH:
                return $date->format('M Y');
            case Gpf_Common_DateUtils::YEAR:
                return $date->getYear();
            case Gpf_Common_DateUtils::WEEK:
                return $date->getWeekStart()->toLocaleDate()." - ".$date->getWeekEnd()->toLocaleDate();
            default:
                return $date->toLocaleDate();
        }

    }

    /**
     * returns difference between dates in units
     * @param date1 - date in format YYYY-MM-DD
     * @param date2 - date in format YYYY-MM-DD
     * @param unit - one of the 'day', 'week', 'month', 'year'
     */
    static public function getDifference($date1, $date2, $unit) {
        $d1 = Gpf_Common_DateUtils::getTimestamp($date1);
        $d2 = Gpf_Common_DateUtils::getTimestamp($date2);

        if($d1 > $d2) {
            $temp = $d1;
            $d1 = $d2;
            $d2 = $temp;
        }

        switch($unit) {
            case Gpf_Common_DateUtils::DAY: return ceil(($d2-$d1)/86400);

            case Gpf_Common_DateUtils::WEEK: return ceil(($d2-$d1)/604800);

            case Gpf_Common_DateUtils::MONTH: return ceil(($d2-$d1)/2628000);

            case Gpf_Common_DateUtils::YEAR: return ceil(($d2-$d1)/31536000);

            case Gpf_Common_DateUtils::NONE: return 0;
        }

        return -1;
    }

    static public function getDateByGroup($year, $month, $day, $unit) {
        switch($unit) {
            case Gpf_Common_DateUtils::DAY: return $year."-".$month."-".$day;

            case Gpf_Common_DateUtils::WEEK: return $year."-".$month."-".$day;

            //case Gpf_Common_DateUtils::MONTH: return $year."-".$month."-".$day;
            case Gpf_Common_DateUtils::MONTH: return $year."-".$month."-".Gpf_Common_DateUtils::getDaysInMonth($month, $year);

            case Gpf_Common_DateUtils::YEAR: return $year."-12-".Gpf_Common_DateUtils::getDaysInMonth($month, $year);

            case Gpf_Common_DateUtils::NONE: return $year."-".$month."-".$day;
        }

        return "0-0-0";
    }

    /**
     * @desc "returns positive value if date2>date1. unit can be one of: Gpf_Common_DateUtils::SECOND, DAY, WEEK, MONTH, YEAR, NONE"
     * @param String $date1
     * @param String $date2
     * @param $unit
     */
    static public function getExactDifference($date1, $date2, $unit) {
        $d1 = Gpf_Common_DateUtils::getTimestamp($date1);
        $d2 = Gpf_Common_DateUtils::getTimestamp($date2);

        switch($unit) {
        	case Gpf_Common_DateUtils::SECOND: return round(($d2-$d1));
        	
            case Gpf_Common_DateUtils::DAY: return round(($d2-$d1)/86400);

            case Gpf_Common_DateUtils::WEEK: return round(($d2-$d1)/604800);

            case Gpf_Common_DateUtils::MONTH: return round(($d2-$d1)/2628000);

            case Gpf_Common_DateUtils::YEAR: return round(($d2-$d1)/31536000);

            case Gpf_Common_DateUtils::NONE: return 0;
        }

        return -1;
    }

    /**
     * transforms date to month date - which is the same date, but day is 1.
     * For example 2008-04-07 will be transformed to 2008-04-01
     *
     * @param string $date
     */
    static public function transformToMonthStart($date) {
        $timestamp = Gpf_Common_DateUtils::getTimestamp($date);
        $newdate = mktime(0, 0, 0, date("m", $timestamp), 1, date("Y", $timestamp));
        return Gpf_Common_DateUtils::getDate($newdate);
    }

    static public function transformToMonthEnd($date) {
        $timestamp = Gpf_Common_DateUtils::getTimestamp($date);
        $newdate = mktime(0, 0, 0, date("m", $timestamp),
        Gpf_Common_DateUtils::getDaysInMonth(date("m", $timestamp), date("Y", $timestamp)),
        date("Y", $timestamp));
        return Gpf_Common_DateUtils::getDate($newdate);
    }

    /**
     * returns part of the date
     *
     * @param date $date in format YYYY-mm-dd
     * @param string $part - one of day/month/year
     * @return string
     */
    static public function getPart($date, $part) {
        $timestamp = Gpf_Common_DateUtils::getTimestamp($date);

        switch($part) {
            case Gpf_Common_DateUtils::DAY: return date("d", $timestamp);
            case Gpf_Common_DateUtils::MONTH: return date("m", $timestamp);
            case Gpf_Common_DateUtils::YEAR: return date("Y", $timestamp);
        }

        return "";
    }

    static private function getDateParts($datetime) {
        $arr = explode(' ', $datetime);
        if(is_array($arr) && count($arr) == 2) {
            return array("date" => $arr[0],
                         "time" => $arr[1]);
        }
        return array("date" => $datetime, "time" => "");
    }

    /**
     * returns datepart of the date
     *
     * @param date $date in format YYYY-mm-dd hh:ii:ss
     * @return string YYYY-mm-dd
     */
    static public function getOnlyDatePart($datetime) {
        $dateParts = self::getDateParts($datetime);
        return $dateParts["date"];
    }

    /**
     * returns timepart of the date
     *
     * @param date $date in format YYYY-mm-dd hh:ii:ss
     * @return string hh:ii:ss
     */
    static public function getOnlyTimePart($datetime) {
        $dateParts = self::getDateParts($datetime);
        return $dateParts["time"];
    }

    /**
     * returns days in month
     *
     * @param $month
     * @param $year
     * @return number
     */
    static public function getDaysInMonth($month, $year) {
        $daysInMonth = array();
        $daysInMonth['01'] = $daysInMonth[1] = 31;
        $daysInMonth['02'] = $daysInMonth[2] = 28;
        $daysInMonth['03'] = $daysInMonth[3] = 31;
        $daysInMonth['04'] = $daysInMonth[4] = 30;
        $daysInMonth['05'] = $daysInMonth[5] = 31;
        $daysInMonth['06'] = $daysInMonth[6] = 30;
        $daysInMonth['07'] = $daysInMonth[7] = 31;
        $daysInMonth['08'] = $daysInMonth[8] = 31;
        $daysInMonth['09'] = $daysInMonth[9] = 30;
        $daysInMonth[10] = 31;
        $daysInMonth[11] = 30;
        $daysInMonth[12] = 31;

        $days = $daysInMonth[$month];
        if(Gpf_Common_DateUtils::isLeapYear($year) && $month == 2) {
            $days++;
        }

        return $days;
    }

    static public function getDateInLocaleFormat($time = '') {
        if($time == '') {
            $time = time();
        }

        $format = Gpf_Common_DateUtils::convertDateFormatJava2PHP(Gpf_Settings_Regional::getInstance()->getDateFormat());

        return date($format, $time);
    }

    static public function getTimeInLocaleFormat($time = '') {
        if($time == '') {
            $time = time();
        }

        $format = Gpf_Common_DateUtils::convertDateFormatJava2PHP(Gpf_Settings_Regional::getInstance()->getTimeFormat());

        return date($format, $time);
    }

    /**
     * Converts java date format (used in e.g. GridPanel) to PHP date format
     * (used in DateField)
     *
     * @param javaFormat
     * e.g. dd-MM-yyyy
     * @return e.g. d-m-y
     */
    static public function convertDateFormatJava2PHP($javaFormat) {
        $conversionTable = array("yyyy" => "Y", "yy" => "y", "MMMMM" => "F", "MMMM" => "F",
     		"MMM" => "M", "MM" => "m", "EEEEEE" => "l", "EEEEE" => "l", "EEEE" => "l", "EEE" => "D", "dd" => "d",
     		"HH" => "H", "mm" => "i", "ss" => "s", "hh" => "h", "A" => "a", "S" => "u" );

        $result = $javaFormat;
        foreach($conversionTable as $key => $replacement) {
            $result = str_replace($key, $replacement, $result);
        }
        return $result;
    }

    /**
     * Get client time.
     * Computed from offset between client and server stored in session.
     *
     * @param integer $serverTime Server time
     * @return integer Client time in seconds from start of unix epoch time
     */
    static public function getClientTime($serverTime) {
        return $serverTime + Gpf_Session::getInstance()->getTimeOffset();
    }

    /**
     * Get server time.
     * Computed from offset between client and server stored in session.
     *
     * @param integer $clientTime Client time
     * @return integer Server time in seconds from start of unix epoch time
     */
    static public function getServerTime($clientTime) {
        return $clientTime - Gpf_Session::getInstance()->getTimeOffset();
    }

    static public function getServerHours($clientHours) {
        $serverHours = Gpf_DateTime::secondsToHours(Gpf_DateTime::hoursToSeconds($clientHours) - Gpf_Session::getInstance()->getTimeOffset());
        return $serverHours < 0 ? 24 + $serverHours : $serverHours;
    }

    /**
     * @return time zone offset in format +/-HH:MM
     */
    static public function getTimeZoneOffset() {

        $timeOffset = Gpf_Session::getInstance()->getTimeOffset();

        $hours = self::addLeadingZero(floor(abs($timeOffset) / 3600));
        $minutes = self::addLeadingZero(floor((abs($timeOffset) % 3600) / 60));

        return ($timeOffset < 0) ? '-' : '+' . $hours . ':' . $minutes;
    }

    static private function addLeadingZero($value) {
        return ($value < 10) ? '0'.$value : $value;
    }

    public static function now() {
        return date(self::DATETIME_FORMAT);
    }

    public static function nowDay() {
        return date("Y-m-d");
    }
    
    public static function firstDayOfCurrentMonth() {
        return date("Y-m-1");
    }

    public static function getNow() {
        return floor(self::getNowSeconds()*1000);
    }

    public static function mysqlDateTime2Timestamp($mysql_timestamp){
        if (preg_match('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $mysql_timestamp, $pieces)
        || preg_match('/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $mysql_timestamp, $pieces)) {
            $unix_time = mktime($pieces[4], $pieces[5], $pieces[6], $pieces[2], $pieces[3], $pieces[1]);
        } elseif (preg_match('/\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}/', $mysql_timestamp)
        || preg_match('/\d{2}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}/', $mysql_timestamp)
        || preg_match('/\d{4}\-\d{2}\-\d{2}/', $mysql_timestamp)
        || preg_match('/\d{2}\-\d{2}\-\d{2}/', $mysql_timestamp)) {
            $unix_time = strtotime($mysql_timestamp);
        } elseif (preg_match('/(\d{4})(\d{2})(\d{2})/', $mysql_timestamp, $pieces)
        || preg_match('/(\d{2})(\d{2})(\d{2})/', $mysql_timestamp, $pieces)) {
            $unix_time = mktime(0, 0, 0, $pieces[2], $pieces[3], $pieces[1]);
        } else {
            $unix_time = time();
        }
        return $unix_time;
    }

    public static function timeToHumanFormat($timestamp) {
        $difference = time() - $timestamp;
        if ($difference < 0) {
            return "not implemented";
        } else if (0 <= $difference && $difference < 86400) {
            return self::_('Today') . ' ' . date("G:i", $timestamp);
        } else if (date("Y", $timestamp) == date("Y")) {
            return date("F d", $timestamp);
        } else {
            return date("F d, Y", $timestamp);
        }
    }

    public static function getHumanRelativeTime($timestamp){
        $difference = time() - $timestamp;
        $periods = array(
        self::_("seconds"), self::_("minutes"), self::_("hours"),
        self::_("days"), self::_("weeks"), self::_("months"),
        self::_("years"), self::_("decades")
        );
        $lengths = array("60","60","24","7","4.35","12","10");

        $is_positive = true;
        if ($difference < 0) { // this was in the past
            $difference = -$difference;
            $is_positive = false;
        }
        for($j = 0; $difference >= $lengths[$j]; $j++) $difference /= $lengths[$j];

        $difference = round($difference);

        if ($is_positive) {
            return self::_("%s %s ago", $difference, $periods[$j]);
        } else {
            return self::_("%s %s to go", $difference, $periods[$j]);
        }
    }

    public static function getNowSeconds() {
        if(!Gpf_Php::isFunctionEnabled('microtime')) {
            return time();
        }
        $microtime = microtime(true);
        return (time() + $microtime - floor($microtime));
    }
    
    public static function getSqlTimeZoneColumn($columnName) {
        return "DATE_ADD($columnName, INTERVAL '" . self::getTimeZoneOffset() . "' HOUR_MINUTE)";
    }

    public static function daysInMonth($month, $day, $year) {
        $firstMonth = 1;
        $lastMonth = 12;
        
        if ($month < $firstMonth || $month > $lastMonth) {
            $year = $year + floor($month/12);
        }
        if ($month < 1) {
            $month = 12+($month%12);
        }
        if (self::getDaysInMonth((($month-1)%12)+1, $year) < $day) {
            return self::getDaysInMonth((($month-1)%12)+1, $year);
        }
        return $day;
    }

}

?>
