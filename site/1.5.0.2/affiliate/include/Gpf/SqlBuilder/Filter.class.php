<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Filter.class.php 32466 2011-05-13 06:58:44Z mkendera $
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
class Gpf_SqlBuilder_Filter extends Gpf_Object {
    const STRING = "S";
    const NUMBER = "N";
    const DATETIME = "D";
    const TIME = "T";
    const OTHER = "O";

    const FILTER_CODE = 0;
    const FILTER_OPERATOR = 1;
    const FILTER_VALUE = 2;

    const RANGE_TODAY = 'T';
    const RANGE_YESTERDAY = 'Y';
    const RANGE_LAST_7_DAYS = 'L7D';
    const RANGE_LAST_30_DAYS = 'L30D';
    const RANGE_LAST_90_DAYS = 'L90D';
    const RANGE_THIS_WEEK = 'TW';
    const RANGE_LAST_WEEK = 'LW';
    const RANGE_LAST_2WEEKS = 'L2W';
    const RANGE_LAST_WORKING_WEEK = 'LWW';
    const RANGE_THIS_MONTH = 'TM';
    const RANGE_LAST_MONTH = 'LM';
    const RANGE_THIS_YEAR = 'TY';
    const RANGE_LAST_YEAR = 'LY';

    /**
     * @var string
     */
    private $code;
    /**
     * @var Gpf_SqlBuilder_Operator
     */
    private $operator;
    /**
     * @var string
     */
    private $value;
    /**
     * @var integer
     */
    private $timeOffset;

    private $operators = array(self::NUMBER => array(),
    self::STRING => array(),
    self::DATETIME => array(),
    self::TIME => array(),
    self::OTHER => array());

    public function __construct($filterArray = NULL) {
        $this->initOperators();
        if ($filterArray != NULL) {
            $this->code = $filterArray[self::FILTER_CODE];
            $this->operator = $this->getOperator($filterArray[self::FILTER_OPERATOR]);
            $this->value = trim($filterArray[self::FILTER_VALUE]);
            $this->timeOffset = Gpf_Session::getInstance()->getTimeOffset();
        }
    }

    private function initOperators() {
        /* String operators */
        $this->addOperator(self::STRING, 'E',  $this->_("equals"),'=', true);
        $this->addOperator(self::STRING, 'NE', $this->_("not equals"), '<>', true);
        $this->addOperator(self::STRING, 'L',  $this->_("is like"), 'LIKE', true);
        $this->addOperator(self::STRING, 'NL', $this->_("is not like"), 'NOT LIKE', true);

        /* DateTime operators */
        $this->addOperator(self::DATETIME, 'D=',  $this->_("equals"),'=', true);
        $this->addOperator(self::DATETIME, 'D>',  $this->_("is greater"),'>', true);
        $this->addOperator(self::DATETIME, 'D<',  $this->_("is lower"),'<', true);
        $this->addOperator(self::DATETIME, 'D>=', $this->_("is greater or equal"),'>=', true);
        $this->addOperator(self::DATETIME, 'D<=', $this->_("is lower or equal"),'<=', true);
        $this->addOperator(self::OTHER,    'DP',  $this->_("is"), '', true);

        $this->addOperator(self::TIME, 'T=',  $this->_("hour equals"),'=', true);
        $this->addOperator(self::TIME, 'T>',  $this->_("hour is greater"),'>', true);
        $this->addOperator(self::TIME, 'T<',  $this->_("hour is lower"),'<', true);
        $this->addOperator(self::TIME, 'T>=', $this->_("hour is greater or equal"),'>=', true);
        $this->addOperator(self::TIME, 'T<=', $this->_("hour is lower or equal"),'<=', true);

        /* Number operators */
        $this->addOperator(self::NUMBER, '=', $this->_("equals"),'=', false);
        $this->addOperator(self::NUMBER, '>', $this->_("is greater"),'>', false);
        $this->addOperator(self::NUMBER, '<', $this->_("is lower"),'<', false);
        $this->addOperator(self::NUMBER, '>=', $this->_("is greater or equal"),'>=', false);
        $this->addOperator(self::NUMBER, '<=', $this->_("is lower or equal"),'<=', false);

        /* Array operators */
        $this->addOperator(self::OTHER, 'IN', $this->_("is in"),'IN', true);
    }

    private function addOperator($type, $code, $name, $sqlCode ,$doQuote) {
        $operator = new Gpf_SqlBuilder_Operator($code, $name, $sqlCode, $doQuote);
        $this->operators[$type][$code] = $operator;
    }

    public function addTo(Gpf_SqlBuilder_WhereClause $whereClause) {
        switch ($this->operator->getCode()) {
            case 'L':
            case 'NL':
                $whereClause->add($this->code, $this->operator->getSqlCode(), '%'.$this->value.'%', 'AND', $this->operator->getDoQuote());
                break;
            case 'DP':
                $this->addDatePresetTo($whereClause);
                break;
            case 'IN':
                $this->addArrayConditionTo($whereClause);
                break;
            case 'T>=':
            case 'T<=':
            case 'T<':
                $whereClause->add("HOUR(".$this->code.")", $this->operator->getSqlCode(), Gpf_Common_DateUtils::getServerHours($this->value), 'AND', $this->operator->getDoQuote());
                break;
            case 'D>':
            case 'D>=':
                $whereClause->add($this->code, $this->operator->getSqlCode(),
                Gpf_DbEngine_Database::getDateString($this->getServerTime(
                Gpf_Common_DateUtils::getTimestamp($this->addTimePartToDate($this->value, '00:00:00'))
                )), 'AND', $this->operator->getDoQuote());
                break;
            case 'D<=':
            case 'D<':
                $whereClause->add($this->code, $this->operator->getSqlCode(),
                Gpf_DbEngine_Database::getDateString($this->getServerTime(
                Gpf_Common_DateUtils::getTimestamp($this->addTimePartToDate($this->value, '23:59:59'))
                )), 'AND', $this->operator->getDoQuote());

                break;
            default:
                if ($this->value == 'NULL') { // Handle null values in conditions
                    $whereClause->add($this->code, $this->operator->getSqlCode(), null, 'AND', true); //$this->operator->getDoQuote());
                } else {
                    $whereClause->add($this->code, $this->operator->getSqlCode(), $this->value, 'AND', true); //$this->operator->getDoQuote());
                }
                break;
        }
    }

    public function matches(Gpf_Data_Record $record) {
        try {
            $value = $record->get($this->getCode());
        } catch (Gpf_Exception $e) {
            return true;
        }
        switch ($this->operator->getCode()) {
            case 'E': return $value == $this->getValue();
            case 'NE': return $value != $this->getValue();
            case 'L': return strpos($value, $this->getValue()) !== false;
            case 'NL': return strpos($value, $this->getValue()) === false;
            case 'IN': return array_search($value, $this->decodeArrayValue()) !== false;
            default: throw new Gpf_Exception('Unimplemented');
            break;
        }
    }

    /**
     * Adds date filter to array
     *
     * @param array $data
     * @return array("dateFrom" => '', "dateTo" => '')
     */
    public function addDateValueToArray($data) {
        switch ($this->operator->getCode()) {
            case 'DP':
                $data = $this->getDatePresetTo();
                break;
            case 'D>':
            case 'D>=':
                $data["dateFrom"] = Gpf_DbEngine_Database::getDateString($this->getServerTime(
                Gpf_Common_DateUtils::getTimestamp($this->addTimePartToDate($this->value, '00:00:00')))
                );
                break;
            case 'D<=':
            case 'D<':
                $data["dateTo"] = Gpf_DbEngine_Database::getDateString($this->getServerTime(
                Gpf_Common_DateUtils::getTimestamp(
                $this->addTimePartToDate($this->value, '23:59:59'))));
                break;
        }

        return $data;
    }

    protected function addTimePartToDate($value, $timepart) {
        if ($this->hasDateTimePart($value)) {
            return $value;
        }

        if ($value = $this->getDatePart($value)) {
            $value .= ' '.$timepart;
            return $value;
        }
        return null;
    }

    private function hasDateTimePart($dateTime) {
        return preg_match('/^[0-9]{4,4}-([0][1-9]|[1][0-2])-([0][1-9]|[1-2][0-9]|[3][0-1]) (([1-9]{1})|([0-1][0-9])|([1-2][0-3])).([0-5][0-9]).([0-5][0-9])$/i', $dateTime);
    }

    private function getDatePart($date) {
        $regex = '(^[0-9]{4,4}-([0][1-9]|[1][0-2])-([0][1-9]|[1-2][0-9]|[3][0-1]))';
        if (preg_match($regex, $date, $regs)) {
            $result = $regs[0];
            return $result;
        }
        return null;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * @param string $operatorCode
     * @return Gpf_SqlBuilder_Operator
     */
    public function getOperator($operatorCode) {
        foreach ($this->operators as $type => $operators) {
            foreach ($operators as $code => $operator) {
                if ($code == $operatorCode) {
                    return $operator;
                }
            }
        }
        throw new Gpf_Exception("Unknown operator $operatorCode");
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($sqlValue) {
        $this->value = $sqlValue;
    }

    /**
     * @return Gpf_SqlBuilder_Operator
     */
    public function getRawOperator() {
        return $this->operator;
    }

    public function setTimeOffset($timeOffset) {
    	$this->timeOffset = $timeOffset;
    }

    private function decodeArrayValue() {
        return explode(",", $this->getValue());
    }

    private function addArrayConditionTo(Gpf_SqlBuilder_WhereClause $whereClause) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        foreach ($this->decodeArrayValue() as $value) {
            $condition->add($this->getCode(), '=', $value, 'OR', $this->operator->getDoQuote());
        }
        $whereClause->addCondition($condition);
    }

    private function addDatePresetTo(Gpf_SqlBuilder_WhereClause $whereClause) {
        $datePreset = $this->decodeDatePreset($this->value);

        if ($datePreset != null) {
            $whereClause->add($this->code, '>=', $datePreset["dateFrom"]);
            $whereClause->add($this->code, '<=', $datePreset["dateTo"]);
        }
    }

    private function getDatePresetTo() {
        return $this->decodeDatePreset($this->value);
    }

    //TODO: refactor
    public function decodeDatePreset($datePreset) {
        $dateFrom = "";
        $dateTo = "";

        $clientTime = $this->getClientTime(time());
        switch ($datePreset) {
            case self::RANGE_TODAY:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,date("m",$clientTime), date("d",$clientTime), date("Y",$clientTime))
                )
                );
                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,date("m",$clientTime), date("d",$clientTime), date("Y",$clientTime))
                )
                );
                break;
            case self::RANGE_YESTERDAY:

                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 1, date("Y",$clientTime))
                )
                );

                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,date("m",$clientTime), date("d",$clientTime) - 1, date("Y",$clientTime))
                )
                );
                break;
            case self::RANGE_LAST_7_DAYS:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 7, date("Y",$clientTime))
                )
                );

                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,date("m",$clientTime), date("d",$clientTime), date("Y",$clientTime))
                )
                );
                break;
            case self::RANGE_LAST_30_DAYS:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 30, date("Y",$clientTime))
                )
                );
                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,date("m",$clientTime), date("d",$clientTime), date("Y",$clientTime))
                )
                );
                break;
            case self::RANGE_LAST_90_DAYS:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 90, date("Y",$clientTime))
                )
                );
                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,date("m",$clientTime), date("d",$clientTime), date("Y",$clientTime))
                )
                );
                break;
            case self::RANGE_THIS_WEEK:
                $dateWeekRange = $this->getWeek(date("Y",$clientTime), date("m",$clientTime), date("d",$clientTime), 1, 7);
                $dateFrom = $dateWeekRange['from'];
                $dateTo = $dateWeekRange['to'];
                break;
            case self::RANGE_LAST_WEEK:
                $dateWeekRange = $this->getWeek(date("Y",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 7, date("Y",$clientTime))),
                date("m",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 7, date("Y",$clientTime))),
                date("d",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 7, date("Y",$clientTime))),
                1, 7);
                $dateFrom = $dateWeekRange['from'];
                $dateTo = $dateWeekRange['to'];
                break;
            case self::RANGE_LAST_2WEEKS:
                $dateWeekRange = $this->getWeek(date("Y",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 14, date("Y",$clientTime))),
                date("m",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 14, date("Y",$clientTime))),
                date("d",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 14, date("Y",$clientTime))),
                1, 14);
                $dateFrom = $dateWeekRange['from'];
                $dateTo = $dateWeekRange['to'];
                break;
            case self::RANGE_LAST_WORKING_WEEK:
                $dateWeekRange = $this->getWeek(date("Y",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 7, date("Y",$clientTime))),
                date("m",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 7, date("Y",$clientTime))),
                date("d",mktime(0,0,0,date("m",$clientTime), date("d",$clientTime) - 7, date("Y",$clientTime))),
                1, 5);
                $dateFrom = $dateWeekRange['from'];
                $dateTo = $dateWeekRange['to'];
                break;
            case self::RANGE_THIS_MONTH:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,date("m",$clientTime), 1, date("Y",$clientTime))
                )
                );
                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,date("m",$clientTime) + 1, 0, date("Y",$clientTime))
                )
                );
                break;
            case self::RANGE_LAST_MONTH:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,date("m",$clientTime) - 1, 1, date("Y",$clientTime))
                )
                );
                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,date("m",$clientTime), 0, date("Y",$clientTime))
                )
                );
                break;
            case self::RANGE_THIS_YEAR:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,1,1, date("Y",$clientTime))
                )
                );
                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,12,31, date("Y",$clientTime))
                )
                );
                break;
            case self::RANGE_LAST_YEAR:
                $dateFrom = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(0,0,0,1,1, date("Y",$clientTime)-1)
                )
                );
                $dateTo = Gpf_DbEngine_Database::getDateString(
                $this->getServerTime(
                mktime(23,59,59,12,31, date("Y",$clientTime)-1)
                )
                );
                break;
            case 'A':
                return null;
                break;
        }

        return array("dateFrom" => $dateFrom, "dateTo" => $dateTo);
    }

    protected function getWeek($year, $month, $day, $dayFrom, $dayTo){
        $w = date('w',mktime(0,0,0,$month, $day, $year));

        $dateRange['from'] = Gpf_DbEngine_Database::getDateString(
        $this->getServerTime(
        mktime(0, 0, 0, $month, $day-$w + $dayFrom, $year)
        )
        );
        $dateRange['to'] = Gpf_DbEngine_Database::getDateString(
        $this->getServerTime(
        mktime(23, 59, 59, $month, $day-$w + $dayTo , $year)
        )
        );

        return $dateRange;
    }

    /**
     * @param $timeStamp
     * @return $timeStamp
     */
    private function getClientTime($serverTime) {
        return $serverTime + $this->timeOffset;
    }

    private function getServerTime($clientTime) {
        return $clientTime - $this->timeOffset;
    }

    public function getStringOperators() {
        return $this->operators[self::STRING];
    }

    public function getNumberOperators() {
        return $this->operators[self::NUMBER];
    }

    public function getDateTimeOperators() {
        return $this->operators[self::DATETIME];
    }
}

?>
