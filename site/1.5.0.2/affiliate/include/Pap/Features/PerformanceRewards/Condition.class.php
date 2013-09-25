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
abstract class Pap_Features_PerformanceRewards_Condition extends Gpf_Object {
    const WHAT_NUMBER_OF_SALES = 'S';
    const WHAT_VALUE_OF_COMMISSIONS = 'C';
    const WHAT_VALUE_OF_TOTAL_COST = 'T';
    const WHAT_VALUE_OF_2TIER_TOTAL_COST = '2';
    const WHAT_NUMBER_OF_RECURRING_SALES = 'U';
    const WHAT_VALUE_OF_TOTAL_COST_IN_RECURRING_SALES = 'R';
    const WHAT_VALUE_OF_TOTAL_COST_AND_TOTAL_COST_IN_RECURRING_SALES = 'Q';
    const WHAT_VALUE_OF_COMMISSIONS_FROM_FIRST_TIER = 'F';
    const WHAT_NUMBER_OF_SALES_AND_RECURRING_SALES = 'G';

    const EQUATION_LOWER_THAN = 'L';
    const EQUATION_HIGHER_THAN = 'H';
    const EQUATION_BETWEEN = 'B';
    const EQUATION_EQUAL_TO = 'E';

    const STATUS_APPROVED = 'A';
    const STATUS_PENDING = 'P';
    const STATUS_DECLINED = 'D';
    const STATUS_FIXED = 'X';
    const STATUS_DESCENDING = "F";
    const STATUS_ASCENDING = "R";
    const STATUS_APPROVED_OR_PENDING = 'O';
    
    /**
     *
     * @var Pap_Features_PerformanceRewards_Rule_Transaction
     */
    protected $rule;
    
    public function __construct(Pap_Features_PerformanceRewards_Rule_Transaction $rule) {
        $this->rule = $rule;
    }
    
    abstract public function computeCommissions();
     
    protected function getStatusText($status) {
        switch ($status) {
            case self::STATUS_APPROVED:
                return $this->_('Approved');
            case self::STATUS_PENDING:
                return $this->_('Pending');
            case self::STATUS_DECLINED:
                return $this->_('Declined');
            case self::STATUS_APPROVED_OR_PENDING:
                return $this->_('Approved or Pending');
            case self::STATUS_ASCENDING:
                return $this->_('Ascending');
            case self::STATUS_DESCENDING:
                return $this->_('Descending');
        }
    }
    
    protected function getDateText($date) {
        switch ($date) {
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_ACTUAL_MONTH:
                return $this->_("Actual month");
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_ACTUAL_YEAR:
                return $this->_("Actual year");
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_ALL_UNPAID_COMMISSIONS:
                return $this->_("All unpaid commissions");
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_LAST_WEEK:
                return $this->_("Last week");
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_LAST_TWO_WEEKS:
                return $this->_("Last two weeks");
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_LAST_MONTH:
                return $this->_("Last month");
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_LAST_YEAR:
                return $this->_("Last year");
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_ALL_TIME:
                return $this->_("All time");
            case Pap_Features_PerformanceRewards_Rule_Transaction::DATE_SINCE_DAY_OF_LAST_MONTH:
                return $this->_("Since %s day of last month", $this->rule->getSince());
        }
    }
    
    public function isValid() {
        return $this->evaluate($this->computeValue());
    }
    
    /**
     *
     * @param Pap_Features_PerformanceRewards_Rule $rule
     * @return Pap_Features_PerformanceRewards_Condition
     */
    public static function create(Pap_Features_PerformanceRewards_Rule_Transaction $rule) {
        $className = self::getClassName($rule->getWhat());
        return new $className($rule);
    }
    
    /**
     *
     * @return Pap_Features_PerformanceRewards_Condition
     */
    public static function toString($code) {
        $className = self::getClassName($code);
        return call_user_func(array($className, 'toString'));
    }
    
    /**
     *
     * @param Pap_Features_PerformanceRewards_Rule $transactionRule
     * @return Pap_Features_PerformanceRewards_Condition
     */
    public static function getClassName($code) {
        switch($code) {
            case self::WHAT_NUMBER_OF_RECURRING_SALES:
                return 'Pap_Features_PerformanceRewards_Condition_RecurringSalesCount';
            case self::WHAT_NUMBER_OF_SALES:
                return 'Pap_Features_PerformanceRewards_Condition_SalesCount';
            case self::WHAT_VALUE_OF_COMMISSIONS:
                return 'Pap_Features_PerformanceRewards_Condition_Commissions';
            case self::WHAT_VALUE_OF_TOTAL_COST:
                return 'Pap_Features_PerformanceRewards_Condition_TotalCost';
            case self::WHAT_VALUE_OF_2TIER_TOTAL_COST:
                return 'Pap_Features_PerformanceRewards_Condition_TotalCost2ndTier';
            case self::WHAT_VALUE_OF_TOTAL_COST_IN_RECURRING_SALES:
                return 'Pap_Features_PerformanceRewards_Condition_TotalCostRecurringSales';
            case self::WHAT_VALUE_OF_TOTAL_COST_AND_TOTAL_COST_IN_RECURRING_SALES:
                return 'Pap_Features_PerformanceRewards_Condition_TotalCostSalesAndRecurringSales';
            case self::WHAT_VALUE_OF_COMMISSIONS_FROM_FIRST_TIER:
                return 'Pap_Features_PerformanceRewards_Condition_CommissionsFromFirstTier';
            case self::WHAT_NUMBER_OF_SALES_AND_RECURRING_SALES:
                return 'Pap_Features_PerformanceRewards_Condition_SalesCountAndRecurringSalesCount';
            default:
                throw new Gpf_Exception('Unsupported condition');
        }
    }
    
    public static function getAllConditions() {
        $list = array();
        $list[] = self::WHAT_NUMBER_OF_RECURRING_SALES;
        $list[] = self::WHAT_NUMBER_OF_SALES;
        $list[] = self::WHAT_VALUE_OF_COMMISSIONS;
        $list[] = self::WHAT_VALUE_OF_TOTAL_COST;
        $list[] = self::WHAT_VALUE_OF_2TIER_TOTAL_COST;
        $list[] = self::WHAT_VALUE_OF_TOTAL_COST_IN_RECURRING_SALES;
        $list[] = self::WHAT_VALUE_OF_COMMISSIONS_FROM_FIRST_TIER;
        $list[] = self::WHAT_VALUE_OF_TOTAL_COST_AND_TOTAL_COST_IN_RECURRING_SALES;
        $list[] = self::WHAT_NUMBER_OF_SALES_AND_RECURRING_SALES;
        return $list;
    }
    
    abstract protected function computeValue();
    
    abstract protected function getString();
    
    protected function getStatuses() {
        if ($this->rule->getStatus() == Pap_Features_PerformanceRewards_Condition::STATUS_APPROVED_OR_PENDING) {
            return array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING);
        }
        return array($this->rule->getStatus());
    }
    
    private function getEquationText($since) {
        switch ($since) {
            case self::EQUATION_LOWER_THAN:
                return $this->_('lower than (<) %s', $this->rule->getEquationValue1());
            case self::EQUATION_HIGHER_THAN:
                return $this->_('higher than (>) %s', $this->rule->getEquationValue1());
            case self::EQUATION_BETWEEN:
                return $this->_('between (>) %s and (<) %s', $this->rule->getEquationValue1(), $this->rule->getEquationValue2());
            case self::EQUATION_EQUAL_TO:
                return $this->_('equal to (=) %s', $this->rule->getEquationValue1());
        }
    }
    
    public function getText() {
        return $this->_('If %s that are %s in %s is %s then ',
            $this->getString(),
            $this->getStatusText($this->rule->getStatus()),
            $this->getDateText($this->rule->getDate()),
            $this->getEquationText($this->rule->getEquation())
            );
    }
    
    protected function evaluate($value) {
        if ($value === null) {
            $value = 0;
        }
        switch ($this->rule->getEquation()) {
            case Pap_Features_PerformanceRewards_Condition::EQUATION_BETWEEN :
                Gpf_log::info('Rule condition: '.$value.' BETWEEN '.$this->rule->getEquationValue1().' and '.$this->rule->getEquationValue2());
                if ($this->rule->getEquationValue1() < $value && $value < $this->rule->getEquationValue2()) {
                    return true;
                }
                break;
            case Pap_Features_PerformanceRewards_Condition::EQUATION_EQUAL_TO :
                Gpf_log::info('Rule condition: '.$value.' EQUAL TO '.$this->rule->getEquationValue1());
                if ($this->rule->getEquationValue1() == $value) {
                    return true;
                }
                break;
            case Pap_Features_PerformanceRewards_Condition::EQUATION_HIGHER_THAN :
                Gpf_log::info('Rule condition: '.$value.' HIGHER THAN '.$this->rule->getEquationValue1());
                if ($this->rule->getEquationValue1() < $value) {
                    return true;
                }
                break;
            case Pap_Features_PerformanceRewards_Condition::EQUATION_LOWER_THAN :
                Gpf_log::info('Rule condition: '.$value.' LOWER THAN '.$this->rule->getEquationValue1());
                if ($this->rule->getEquationValue1() > $value) {
                    return true;
                }
                break;
            default : throw new Gpf_Exception('Unsupported equation');
        }
        return false;
    }
    
    public function getCurrentUserId() {
        return $this->rule->getTransaction()->getUserId();
    }
}

?>
