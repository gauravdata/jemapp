<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 23478 2009-02-12 14:48:17Z aharsani $
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
class Pap_Common_TopUrlGridBase extends Pap_Common_StatsGrid {

    public function __construct() {
        parent::__construct(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, 'r');
    }

    protected function createResultSelect() {
        $this->groupColumn = $this->getGroupByColumn();
        $this->mainTableColumn = $this->groupColumn;
        parent::createResultSelect();
    }

    protected function initViewColumns() {
        $this->addViewColumn('id', $this->_("ID"), true);
        $this->addViewColumn('referrerurl', $this->_("Referrer URL"), true);
        $this->addViewColumn('salesCount', $this->_("Sale count"), true);
        $this->addViewColumn('salesTotal', $this->_("Sale total cost"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('commissions', $this->_("Sale commision"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addAllActionsViewColumns();
    }
    protected function buildWhere() {
        parent::buildWhere();
        $whereCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $whereCondition->add('r.'.Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_SALE);
        $whereCondition->add('r.'.Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_ACTION, "OR");
        $this->_selectBuilder->where->addCondition($whereCondition);        
        if ($this->filters->getSize() <= 0 || ($rstatus = $this->filters->getFilterValue('rstatus')) == '') {
            $rstatus = Pap_Common_Constants::STATUS_APPROVED;
        }
        $this->_selectBuilder->where->add('r.'.Pap_Db_Table_Transactions::R_STATUS, 'in', preg_split('/,/', $rstatus));
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('r.'.$this->groupColumn);
        $this->addDataColumn('referrerurl', '');
        $this->addDataColumn('dateinserted', 'r.dateinserted');
        $this->addDataColumn('countrycode', 'r.'.Pap_Db_Table_Transactions::COUNTRY_CODE);
        $this->initStatColumns();
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('referrerurl', 300, 'N');
        $this->addDefaultViewColumn('salesCount', 30, 'D');
    }

    protected function addSelect($sql, $alias) {
        if ($alias === 'referrerurl') {
            parent::addSelect('r.'.$this->groupColumn, $alias);
            return;
        }
        parent::addSelect($sql, $alias);
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Transactions::getName(), 'r');
        $this->buildStatsFrom();
    }

    protected function buildGroupBy() {
        $this->_selectBuilder->groupBy->add('r.'.$this->groupColumn);
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        if ($filter->getCode() === 'search') {
            $this->addSearch($filter);
        }
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('r.'.$this->groupColumn, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $this->_selectBuilder->where->addCondition($condition);
    }

    /**
     * @return String
     */
    protected function getGroupByColumn() {
        $groupByFilter = $this->filters->getFilter("groupby");
       	if ((count($groupByFilter) > 0) && ($groupByFilter[0]->getValue() === 'lclick')) {
       	    return Pap_Db_Table_Transactions::LAST_CLICK_REFERER;
       	}
       	return Pap_Db_Table_Transactions::FIRST_CLICK_REFERER;
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $params = parent::getStatsParameters();
        return $this->addParamsWithDateRangeFilter($params);
    }

    /**
     * @param $select
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $preffixes = $select->where->getUniqueTablePreffixes();
        $preffixes['r'] = 'r';

        $countInner = new Gpf_SqlBuilder_SelectBuilder();
        $countInner->select->add('r.*');
        $countInner->from = clone $select->from;
        $countInner->from->prune($preffixes);
        $countInner->where = $select->where;
        $countInner->where->add($this->groupColumn, 'is not', 'NULL', 'AND', false);
        $countInner->groupBy = $select->groupBy;
        $countInner->having = $select->having;

        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from->addSubselect($countInner, 'count');

        return $count;
    }

    /**
     * @param Pap_Stats_Params $statParams
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function getTransactionStatsSelect(Pap_Stats_Params $statParams) {
        $this->columnsToDatabaseNames['referrerurl'] = Pap_Common_StatsGrid::GROUP_COLUMN_ALIAS;
        $transactionStats = new Pap_Stats_Computer_TransactionsStatsBuilder($statParams, $this->groupColumn, Pap_Common_StatsGrid::GROUP_COLUMN_ALIAS);
        $transactionStats->getTransactionsWhereClause()->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_SALE);
        $transactionStats->getTransactionsWhereClause()->add($this->groupColumn, 'is not', 'NULL', 'AND', false);
        $transactionStatsSelect = $transactionStats->getStatsSelect();
        $transactionStatsSelect->orderBy->add($this->columnsToDatabaseNames[$this->_sortColumn], $this->_sortAsc);
        $this->initLimit();
        $transactionStatsSelect->limit->set($this->offset, $this->limit);
        return $transactionStatsSelect;
    }

    protected function buildLimit() {
    }

    /**
     * @param Pap_Stats_Params $statParams
     * @param $tableAlias
     */
    protected function addJoinSelectToSelectBuilder(Gpf_SqlBuilder_SelectBuilder $select, $tableAlias) {
        $this->_selectBuilder->from->addRightJoin('('.$select->toString().')',
                    $tableAlias, $tableAlias.'.'.self::GROUP_COLUMN_ALIAS.'='.$this->mainTablePreffix. '.'. $this->mainTableColumn);
    }
}
