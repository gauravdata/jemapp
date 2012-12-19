<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
abstract class Pap_Features_PerformanceRewards_Rule extends Pap_Db_Rule {

    const DATE_ACTUAL_MONTH = 'TM';
    const DATE_ACTUAL_YEAR = 'TY';
    const DATE_ALL_UNPAID_COMMISSIONS = 'AUC';
    const DATE_LAST_WEEK = 'LW';
    const DATE_LAST_TWO_WEEKS = 'L2W';
    const DATE_LAST_MONTH = 'LM';
    const DATE_ALL_TIME = 'AT';
    const DATE_SINCE_DAY_OF_LAST_MONTH = 'SD';
    const DATE_LAST_YEAR = 'LY';

    const BONUS_TYPE_AMOUNT = '$';
    const BONUS_TYPE_PERCENTAGE = '%';


    private $condition = null;

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     *
     * @return Pap_Features_PerformanceRewards_Condition
     */
    private function createCondition() {
        return Pap_Features_PerformanceRewards_Condition::create($this);
    }

    /**
     *
     *
     * @return Pap_Features_PerformanceRewards_Condition
     */
    public function getCondition() {
        if ($this->condition === null) {
            $this->condition = $this->createCondition();
        }
        return $this->condition;
    }

    public function isConditionValid() {
        return $this->getCondition()->isValid();
    }

    public function executeAction() {
        if ($this->isConditionValid()) {
            $this->getActionObject()->execute();
        } else {
            Gpf_Log::debug('Condition of rule is not valid - skipping');
        }
    }

    protected function getActionObject() {
        $this->action = Pap_Features_PerformanceRewards_Action::create($this);
        return $this->action;
    }


    /**
     *
     * @return Gpf_DateTime_Range
     */
    public function getDateRange(Gpf_DateTime $now = null) {
        if($now === null) {
            $now = new Gpf_DateTime();
        }
        if ($this->getDate() == Pap_Features_PerformanceRewards_Rule::DATE_ALL_TIME
        || $this->getDate() == Pap_Features_PerformanceRewards_Rule::DATE_ALL_UNPAID_COMMISSIONS) {
            $range = new Gpf_DateTime_Range();
            return $range;
        }

        if ($this->getDate() == Pap_Features_PerformanceRewards_Rule::DATE_SINCE_DAY_OF_LAST_MONTH) {
            $from = $now->getMonthStart();
            $from->addMonth(-1);
            $from->addDay($this->getSince() - 1);
            $to = $now->getMonthStart();
            $to->addDay(-1);
            return new Gpf_DateTime_Range($from, $to);
        }
        $filter = new Gpf_SqlBuilder_Filter();
        $result = $filter->decodeDatePreset($this->getDate());
        return new Gpf_DateTime_Range(new Gpf_DateTime($result['dateFrom']), new Gpf_DateTime($result['dateTo']));
    }

    public function getString() {
        return $this->getCondition()->getText() . $this->getActionObject()->getString();
    }
}

?>
