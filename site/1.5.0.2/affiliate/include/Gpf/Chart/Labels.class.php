<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DataRecordSet.class.php 21931 2008-10-28 11:12:45Z mbebjak $
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
class Gpf_Chart_Labels {

    private $labels = array();
    
    private function addLabel($date, $timeGroupBy) {
        $this->labels[] = Gpf_Common_DateUtils::formatByUnit($date, $timeGroupBy);
    }
    
    private function makeMonthLabels($dateCurrent, $dateTo) {
        while (($dateCurrent->compare($dateTo) < 0) || ($dateCurrent->getMonth()==$dateTo->getMonth())) {
            $this->addLabel($dateCurrent, Gpf_Common_DateUtils::MONTH);
            $dateCurrent->addMonth();
        }
    }
    
    private function makeDayLabels($dateCurrent, $dateTo){
        while ($dateCurrent->compare($dateTo) <= 0) {
            $this->addLabel($dateCurrent, Gpf_Common_DateUtils::DAY);
            $dateCurrent->addDay();
        }
    }
    
    private function makeWeekLabels($dateCurrent, $dateTo){
        while ($dateCurrent->compare($dateTo->getWeekEnd()) < 0) {
            $this->addLabel($dateCurrent, Gpf_Common_DateUtils::WEEK);
            $dateCurrent->addWeek();
        }
    }
    
    public function __construct(Gpf_DateTime $from, Gpf_DateTime $to, $timeGroupBy) {
        $dateCurrent = clone $from->getClientTime();

        if ($timeGroupBy == Gpf_Common_DateUtils::DAY) {
            $this->makeDayLabels($dateCurrent, $to->getClientTime());
            return;
        } else if ($timeGroupBy == Gpf_Common_DateUtils::MONTH) {
            $this->makeMonthLabels($dateCurrent, $to->getClientTime());
            return;
        }
        $this->makeWeekLabels($dateCurrent, $to->getClientTime());
    }
    
    public function getLabels() {
        return $this->labels;
    }
}

?>
