<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Filters.class.php 28557 2010-06-18 11:01:28Z vzeman $
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
class Gpf_Db_Table_Filters extends Gpf_DbEngine_Table {
	const FILTER_ID = "filterid";
	const NAME = "name";
	const USER_ID = "userid";
	const FILTER_TYPE = "filtertype";
	const PRESET = "preset";

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_filters');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::FILTER_ID, 'char', 8, true);
        $this->createColumn(self::NAME, 'char', 100);
        $this->createColumn(self::USER_ID, 'char', 8);
        $this->createColumn(self::FILTER_TYPE, 'char', 100);
        $this->createColumn(self::PRESET, 'char', 1);
    }

    protected function initConstraints() {
       $this->addCascadeDeleteConstraint(self::FILTER_ID, Gpf_Db_Table_FilterConditions::FILTER_ID, new Gpf_Db_FilterCondition());
    }

    /**
     * @param filterType
     * @service filter read
     */
    public function get(Gpf_Rpc_Params $params) {
        return $this->getFilters($params->get('filterType'));
    }

    /**
     * @param filterType
     * @service filter read
     */
    public function getFilters($filterType) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('f.userid', '=', Gpf_Session::getAuthUser()->getUserId(), 'OR');
        $condition->add('f.userid', '=', null, 'OR');
        $condition->add('f.userid', '=', '', 'OR');

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('f.filterid', 'id');
        $selectBuilder->select->add('f.name', 'name');
        $selectBuilder->select->add('c.fieldid', 'fieldid');
        $selectBuilder->select->add('c.sectioncode', 'sectioncode');
        $selectBuilder->select->add('c.code', 'code');
        $selectBuilder->select->add('c.operator', 'operator');
        $selectBuilder->select->add('c.value', 'value');
        $selectBuilder->from->add(Gpf_Db_Table_Filters::getName(), 'f');
        $selectBuilder->from->addLeftJoin(Gpf_Db_Table_FilterConditions::getName(), 'c', 'f.filterid=c.filterid');
        $selectBuilder->where->add('f.filtertype', '=', $filterType);
        $selectBuilder->where->addCondition($condition);
        $selectBuilder->orderBy->add('f.name');
        $selectBuilder->orderBy->add('f.filterid');
        $selectBuilder->orderBy->add('c.sectioncode');

        $response = new Gpf_Data_RecordSet();
        $response->load($selectBuilder);

        foreach ($response as $filter) {
            $filter->set('name', $this->_localize($filter->get('name')));
        }

        return $response;
    }

    /**
     * @param filterType
     * @service filter read
     */
    public function getFilterNames(Gpf_Rpc_Params $params) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('f.userid', '=', Gpf_Session::getAuthUser()->getUserId(), 'OR');
        $condition->add('f.userid', '=', null, 'OR');

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('f.filterid', 'id');
        $selectBuilder->select->add('f.name', 'name');
        $selectBuilder->from->add(Gpf_Db_Table_Filters::getName(), 'f');
        $selectBuilder->where->add('f.filtertype', '=', $params->get('filterType'));
        $selectBuilder->where->addCondition($condition);
        $selectBuilder->orderBy->add('f.name');

        $response = new Gpf_Data_RecordSet();
        $response->load($selectBuilder);
        return $response;
    }

}

?>
