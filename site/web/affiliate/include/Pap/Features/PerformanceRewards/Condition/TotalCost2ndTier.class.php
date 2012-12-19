<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Features_PerformanceRewards_Condition_TotalCost2ndTier extends Pap_Features_PerformanceRewards_Condition {
    const VALUE = 'value';
    const TRANSACTION_ALIAS = 't';
    /**
     * @var Gpf_SqlBuilder_SelectBuilder
     */
    private $sqlBuilder;

    public function __construct(Pap_Features_PerformanceRewards_Rule_Transaction $rule) {
        parent::__construct($rule);
        $this->sqlBuilder = new Gpf_SqlBuilder_SelectBuilder();
    }

    protected function computeValue() {
        $this->sqlBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $this->sqlBuilder->select->add("MAX(".self::TRANSACTION_ALIAS.'.'.Pap_Db_Table_Transactions::TOTAL_COST.")", self::VALUE);
        $this->buildSql();
        $this->sqlBuilder->groupBy->add(Pap_Db_Table_Transactions::SALE_ID);
        
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add("sum(s." . self::VALUE.')', self::VALUE);
        $select->from->addSubselect($this->sqlBuilder, 's');
        
        $record = $select->getOneRow();
        return $record->get(self::VALUE);
    }

    public function computeCommissions() {
        $this->sqlBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $this->sqlBuilder->select->add("SUM(".self::TRANSACTION_ALIAS.'.'.Pap_Db_Table_Transactions::COMMISSION.")", self::VALUE);
        $this->buildSql();
        $record = $this->sqlBuilder->getOneRow();
        return $record->get(self::VALUE);
    }

    private function buildSql() {
        $this->sqlBuilder->from->add(Pap_Db_Table_Transactions::getName(), self::TRANSACTION_ALIAS);
        $this->buildWhere();
        $this->setUserFilter();
        $this->setStatusWhere($this->sqlBuilder->where);
        $this->rule->setTransactionsWhere($this->sqlBuilder->where, self::TRANSACTION_ALIAS);
    }

    private function setTier($tier) {
        $this->sqlBuilder->where->add(self::TRANSACTION_ALIAS.'.'.Pap_Db_Table_Transactions::TIER, '=', $tier);
    }

    private function setType($type, $operator = '=') {
        $this->sqlBuilder->where->add(self::TRANSACTION_ALIAS.'.'.Pap_Db_Table_Transactions::R_TYPE, $operator, $type);
    }


    private function setStatusWhere(Gpf_SqlBuilder_WhereClause $where) {
        $where->add(self::TRANSACTION_ALIAS.'.'.Pap_Db_Table_Transactions::R_STATUS, 'IN', $this->getStatuses());
    }

    protected function buildWhere() {
        $this->setTier(1);
        $this->setType(Pap_Db_Transaction::TYPE_SALE);
    }

    public function getString() {
        return self::toString();
    }

    public static function toString() {
        return Gpf_Lang::_("value of direct subaffiliates total cost");
    }
    
    public function getCurrentUserId() {
        $user = new Pap_Db_User();
        $user->setId($this->rule->getTransaction()->getUserId());
        $user->load();
        return $user->getParentUserId();
    }

    private function setUserFilter() {
        $this->sqlBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'u',
        self::TRANSACTION_ALIAS.'.'.Pap_Db_Table_Transactions::USER_ID.'='.'u.'.Pap_Db_Table_Users::ID);
        $this->sqlBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'up',
            'up.'.Pap_Db_Table_Users::PARENTUSERID.'='.'u.'.Pap_Db_Table_Users::PARENTUSERID);
        $this->sqlBuilder->where->add('up.'.Pap_Db_Table_Users::ID,'=', $this->rule->getUserId());
        $this->sqlBuilder->where->add('up.'.Pap_Db_Table_Users::PARENTUSERID,'!=', null);
    }
}
?>
