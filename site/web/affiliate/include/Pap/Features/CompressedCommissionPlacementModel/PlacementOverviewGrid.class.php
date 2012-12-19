<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Matej Kendera
 *   @since Version 1.0.0
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
class Pap_Features_CompressedCommissionPlacementModel_PlacementOverviewGrid extends Pap_Common_StatsGrid {

    public function __construct() {
        parent::__construct(Pap_Stats_Table::USERID, 'u');
    }

    /**
     * @service affiliate read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    protected function initViewColumns() {
        $this->addViewColumn('id', $this->_("User ID"), true);
        $this->addViewColumn(Pap_Db_Table_Users::REFID, $this->_("Referral ID"), true);
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, $this->_("Username"), true);
        $this->addViewColumn('value', $this->_('Rule value'), true);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('u.userid');
        $this->addDataColumn('userid', 'u.userid');
        $this->addDataColumn(Pap_Db_Table_Users::REFID, 'u.refid');
        $this->addDataColumn('firstname', 'au.firstname');
        $this->addDataColumn('lastname', 'au.lastname');
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::USERNAME, 'au.username');
        $this->initStatColumns();
    }

    protected function initRequiredColumns() {
        $this->addRequiredColumn('commissions');
        $this->addRequiredColumn('salesCount');
        $this->addRequiredColumn('salesTotal');
        $this->addRequiredColumn('userid');
        $this->addRequiredColumn('firstname');
        $this->addRequiredColumn('lastname');
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('id', 60, 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Users::REFID, 60, 'N');
        $this->addDefaultViewColumn('name', 120, 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, 120, 'N');
        $this->addDefaultViewColumn('value', 40, 'N');
        $this->addDefaultViewColumn(self::ACTIONS, 60, 'N');
    }

    protected function buildFrom() {
        if ($this->filters->isFilter('orderid')) {
            $transSelect = new Gpf_SqlBuilder_SelectBuilder();
            $transSelect->select->setDistinct();
            $transSelect->select->add(Pap_Db_Table_Transactions::USER_ID);
            $transSelect->from->add(Pap_Db_Table_Transactions::getName());
            $this->addOrderIdFilterToSelect($transSelect, $this->filters);
            $this->_selectBuilder->from->addSubselect($transSelect, 't');
            $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(),
            'u', 'u.userid=t.userid');  
        } else {
            $this->_selectBuilder->from->add(Pap_Db_Table_Users::getName(), 'u');
        }

        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(),
            'gu', 'u.accountuserid=gu.accountuserid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),
            'au', 'au.authid=gu.authid');

        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Users::getName(),
            'pu', 'u.parentuserid=pu.userid');
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_Users::getName(),
            'pgu', 'pu.accountuserid=pgu.accountuserid');
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_AuthUsers::getName(),
            'pau', 'pau.authid=pgu.authid');

        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_FieldGroups::getName(),
            'pay', 'pay.fieldgroupid=u.payoutoptionid AND pay.rtype=\'P\' AND pay.rstatus=\'' .
        Gpf_Db_FieldGroup::ENABLED . '\'');

        $this->buildStatsFrom();
    }

    private function getStatuses() {
        if (Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_STATUS) == Pap_Features_PerformanceRewards_Condition::STATUS_APPROVED_OR_PENDING) {
            return array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING);
        }
        return array(Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_STATUS));
    }

    private function getRuleNameByCondition($ruleWhat) {
        switch ($ruleWhat) {
            case Pap_Features_PerformanceRewards_Condition::WHAT_VALUE_OF_COMMISSIONS:
                return 'tr.commission';
            case Pap_Features_PerformanceRewards_Condition::WHAT_VALUE_OF_TOTAL_COST:
                return 'tr.totalcost';
            case Pap_Features_PerformanceRewards_Condition::WHAT_NUMBER_OF_SALES:
                return 'tr.count';
        }
    }

    private function getValueColumnByCondition($ruleWhat) {
        switch ($ruleWhat) {
            case Pap_Features_PerformanceRewards_Condition::WHAT_VALUE_OF_COMMISSIONS:
                return 'commissions';
            case Pap_Features_PerformanceRewards_Condition::WHAT_VALUE_OF_TOTAL_COST:
                return 'salesTotal';
            case Pap_Features_PerformanceRewards_Condition::WHAT_NUMBER_OF_SALES:
                return 'salesCount';
        }
    }

    private function getReachedConditionFilterValue() {
        $acceprConditionFilter = $this->filters->getFilter('reachedCondition');
        if (count($acceprConditionFilter) === 0) {
            return Gpf::YES;
        }
        return $acceprConditionFilter[0]->getValue();
    }

    /**
     * @param Gpf_SqlBuilder_SelectBuilder $select
     */
    private function insertRuleToWhereCondition(Gpf_SqlBuilder_SelectBuilder $select) {
        $value1 = Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION_VALUE1);
        $value2 = Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION_VALUE2);
        $what = $this->getRuleNameByCondition(Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_WHAT));
        switch (Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION)) {
            case 'L':
                $select->where->addCondition($this->createWhereCondition($what, ($this->getReachedConditionFilterValue()==Gpf::YES)?'<':'>=', $value1));
                break;
            case 'H':
                $select->where->addCondition($this->createWhereCondition($what, ($this->getReachedConditionFilterValue()==Gpf::YES)?'>':'<=', $value1));
                break;
            case 'B':
                if ($this->getReachedConditionFilterValue()==Gpf::YES) {
                    $compoundCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
                    $compoundCondition->add($what, 'BETWEEN', $value1 . ' AND ' . $value2, 'AND', false);
                    if ($value1 <= 0 && $value2 >= 0) {
                        $compoundCondition->add($what, '=', null, 'OR');
                    }
                    $select->where->addCondition($compoundCondition);
                } else {
                    $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
                    $condition->addCondition($this->createWhereCondition($what, '<', $value1));
                    $condition->addCondition($this->createWhereCondition($what, '>', $value2), 'OR');
                    $select->where->addCondition($condition);
                }
                break;
            case 'E':
                $select->where->addCondition($this->createWhereCondition($what, ($this->getReachedConditionFilterValue()==Gpf::YES)?'=':'!=', $value1));
                break;
        }
    }

    private function createWhereCondition($operand, $operator, $secondOperand) {
        $compoundCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $compoundCondition->add($operand, $operator, $secondOperand);
        return $compoundCondition;
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add('u.deleted', '=', Gpf::NO);
        $this->_selectBuilder->where->add('u.rtype', '=', Pap_Application::ROLETYPE_AFFILIATE);

        $this->insertRuleToWhereCondition($this->_selectBuilder);
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $params = new Pap_Stats_Params();

        $dateRangeFilter = new Gpf_SqlBuilder_Filter();
        $dateRange = $dateRangeFilter->decodeDatePreset(Pap_Features_CompressedCommissionPlacementModel_Processor::getRecurrencePreset());
        $params->setDateRange($dateRange['dateFrom'], $dateRange['dateTo']);

        $params->setStatus($this->getStatuses());
        return $params;
    }

    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
        $inputResult = parent::afterExecute($inputResult);
        $inputResult->addColumn('value');

        foreach ($inputResult as $record) {
            $record->set('value', $record->get($this->getValueColumnByCondition(Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_WHAT))));
        }
        return $inputResult;
    }

    protected function buildOrder() {
        if ($this->_sortColumn == "name") {
            $this->_selectBuilder->orderBy->add("firstname", $this->_sortAsc, 'au');
            $this->_selectBuilder->orderBy->add("lastname", $this->_sortAsc, 'au');
            return;
        }
        parent::buildOrder();
    }

    /**
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();

        return $filterFields->getRecordSet();
    }

    /**
     * @param Gpf_SqlBuilder_SelectBuilder $select
     * @param Gpf_Rpc_FilterCollection $allFilters
     */
    private function addOrderIdFilterToSelect(Gpf_SqlBuilder_SelectBuilder $select, Gpf_Rpc_FilterCollection $allFilters) {
        $filter = $this->getFirstElement($allFilters->getFilter('orderid'));
        if ($filter === null) {
            return;
        }

        $orderIds = preg_split("/[,;(\n)]/", $filter->getValue());
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();

        for ($i = 0; $i < count($orderIds); $i++) {
            if(trim($orderIds[$i]) != '') {
                $condition->add('orderid', 'LIKE', '%'.trim($orderIds[$i]).'%', 'OR');
            }
        }

        $select->where->addCondition($condition);
    }

    private function getFirstElement($array) {
        foreach ($array as $element) {
            return $element;
        }
        return null;
    }
}
?>
