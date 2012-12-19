<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
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
abstract class Pap_Common_Banner_BannersGrid extends Pap_Common_StatsGrid {

    protected $statsDateParams = array();

    /**
     * @var Pap_Common_User
     */
    protected $user;
    /**
     * @var Pap_Common_Banner_Factory
     */
    protected $bannerFactory;

    function __construct() {
        parent::__construct(Pap_Stats_Table::BANNERID, 'b');
        $this->user = new Pap_Common_User();
        $this->bannerFactory = new Pap_Common_Banner_Factory();
        $this->statsDateParams = array("dateFrom" => date("Y-m-d H:i:s", Gpf_DateTime::MIN_TIMESTAMP), "dateTo" => date("Y-m-d H:i:s", time()));
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('b.'.Pap_Db_Table_Banners::ID);
        $this->addDataColumn('bannerid', 'b.bannerid');
        $this->addDataColumn('accountid', 'b.accountid');
        $this->addDataColumn('campaignid', 'b.campaignid');
        $this->addDataColumn('campaignname', 'c.name');
        $this->addDataColumn('rtype', 'b.rtype');
        $this->addDataColumn('rstatus', 'b.rstatus');
        $this->addDataColumn('name', 'b.name');
        $this->addDataColumn('destinationurl', 'b.destinationurl');
        $this->addDataColumn('dateinserted', 'b.dateinserted');
        $this->addDataColumn('size', 'b.size');
        $this->addDataColumn('data1', 'b.data1');
        $this->addDataColumn('data2', 'b.data2');
        $this->addDataColumn('data3', 'b.data3');
        $this->addDataColumn('data4', 'b.data4');
        $this->addDataColumn('data5', 'b.data5');
        $this->addDataColumn('data6', 'b.data6');
        $this->addDataColumn('data7', 'b.data7');
        $this->addDataColumn('data8', 'b.data8');
        $this->addDataColumn('data9', 'b.data9');
        $this->addDataColumn('rorder', 'b.rorder');
        $this->addDataColumn('ctype', 'c.rtype');
        $this->addDataColumn('wrapperid', 'b.wrapperid');
        $this->addDataColumn('description', 'b.description');
        $this->addDataColumn('seostring', 'b.seostring');

        $this->initStatColumns();
    }

    protected function initRequiredColumns() {
       $this->addRequiredColumn('bannerid');
       $this->addRequiredColumn('accountid');
       $this->addRequiredColumn('name');
        $this->addRequiredColumn('rtype');
        $this->addRequiredColumn('rstatus');
        $this->addRequiredColumn('size');
        $this->addRequiredColumn('data1');
    	$this->addRequiredColumn('data2');
    	$this->addRequiredColumn('data3');
    	$this->addRequiredColumn('data4');
    	$this->addRequiredColumn('data5');
    	$this->addRequiredColumn('data6');
    	$this->addRequiredColumn('data7');
    	$this->addRequiredColumn('data8');
    	$this->addRequiredColumn('data9');
        $this->addRequiredColumn('campaignname');
        $this->addRequiredColumn('campaignid');
        $this->addRequiredColumn('destinationurl');
        $this->addRequiredColumn('wrapperid');
        $this->addRequiredColumn('seostring');
        $this->addRequiredColumn('description');
    }

    protected function buildFrom() {
        $statParams = $this->getStatsParameters();
        $this->_selectBuilder->from->add(Pap_Db_Table_Banners::getName(), 'b');
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Campaigns::getName(),
            'c', 'b.campaignid=c.campaignid');
        $this->buildFilter();
        $this->buildStatsFrom();
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addFilterSearch($filter);
                break;
            case "type":
                $this->addFilterType($filter);
                break;
            case "date":
                $this->statsDateParams = $filter->addDateValueToArray($this->statsDateParams);
                break;
        }
    }

    protected function buildFilter() {
        if ($this->filters->getSize() == 0 ) {
            $this->_selectBuilder->where->add('b.rstatus', '<>', Pap_Db_Banner::STATUS_HIDDEN);
        } else {
            parent::buildFilter();
        }
    }

    /**
     * @return Pap_Stats_Params
     */
    protected function getStatsParameters() {
        $params = parent::getStatsParameters();
        return $this->addParamsWithDateRangeFilter($params);
    }

    private function addFilterSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('b.bannerid', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('b.name', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('b.destinationurl', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('b.data1', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('b.data2', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('b.data3', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('b.data4', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $this->_selectBuilder->where->addCondition($condition);
    }

    private function addFilterType(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('b.rtype', 'in', explode(",", $filter->getValue()));
        $this->_selectBuilder->where->addCondition($condition);
    }
}
?>
