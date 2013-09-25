<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: TransactionReportsGrid.class.php 16621 2008-03-21 09:37:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Merchants_Reports_ClicksGrid extends Gpf_View_GridService {

	/**
	 * @var Gpf_SqlBuilder_SelectBuilder
	 */
	protected $rawClicksSelect;

	protected function initViewColumns() {
		//Not sortable columns (optimized to speed)
		$this->addViewColumn(Pap_Db_Table_RawClicks::ID, $this->_("ID"), true);
		$this->addViewColumn("affiliate", $this->_("Affiliate"), false);
		$this->addViewColumn("banner", $this->_("Banner"), false);
		$this->addViewColumn("campaign", $this->_("Campaign"), false);
		$this->addViewColumn("parentbanner", $this->_("Parent banner"), false);

		$this->addViewColumn(Pap_Db_Table_RawClicks::COUNTRYCODE, $this->_("Country code"), true, Gpf_View_ViewColumn::TYPE_COUNTRYCODE);
		$this->addViewColumn(Pap_Db_Table_RawClicks::RTYPE, $this->_("Type"), true);
		$this->addViewColumn(Pap_Db_Table_RawClicks::DATETIME, $this->_("Date"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
		$this->addViewColumn(Pap_Db_Table_RawClicks::REFERERURL, $this->_("Referer url"), true);
		$this->addViewColumn(Pap_Db_Table_RawClicks::IP, $this->_("IP"), true, Gpf_View_ViewColumn::TYPE_IP);
		$this->addViewColumn("channelname", $this->_("Channel"), true);
		$this->addViewColumn(Pap_Db_Table_RawClicks::DATA1, $this->_("Extra data 1"), true);
		$this->addViewColumn(Pap_Db_Table_RawClicks::DATA2, $this->_("Extra data 2"), true);
	}

	protected function initDataColumns() {
		$this->setKeyDataColumn(Pap_Db_Table_RawClicks::ID);
		$this->addDataColumn(Pap_Db_Table_RawClicks::ID, "rc.".Pap_Db_Table_RawClicks::ID);
		$this->addDataColumn("userid", "rc.".Pap_Db_Table_RawClicks::USERID);
		$this->addDataColumn("username", "au.username");
		$this->addDataColumn("firstname", "au.firstname");
		$this->addDataColumn("lastname", "au.lastname");
		$this->addDataColumn("banner", "b.".Pap_Db_Table_Banners::NAME);
		$this->addDataColumn("parentbanner", "pb.".Pap_Db_Table_Banners::NAME);
		$this->addDataColumn(Pap_Db_Table_RawClicks::CAMPAIGNID, "rc.".Pap_Db_Table_RawClicks::CAMPAIGNID);
        $this->addDataColumn(Pap_Db_Table_RawClicks::COUNTRYCODE, "rc.".Pap_Db_Table_RawClicks::COUNTRYCODE);
		$this->addDataColumn("campaign", "c.name");
		$this->addDataColumn(Pap_Db_Table_RawClicks::RTYPE, "rc.".Pap_Db_Table_RawClicks::RTYPE);
		$this->addDataColumn(Pap_Db_Table_RawClicks::DATETIME, "rc.".Pap_Db_Table_RawClicks::DATETIME);
		$this->addDataColumn(Pap_Db_Table_RawClicks::REFERERURL, "rc.".Pap_Db_Table_RawClicks::REFERERURL);
		$this->addDataColumn(Pap_Db_Table_RawClicks::IP, "rc.".Pap_Db_Table_RawClicks::IP);
		$this->addDataColumn(Pap_Db_Table_RawClicks::BROWSER, "rc.".Pap_Db_Table_RawClicks::BROWSER);
		$this->addDataColumn("channelname", "ch.".Pap_Db_Table_Channels::NAME);
		$this->addDataColumn(Pap_Db_Table_RawClicks::CHANNEL, "rc.".Pap_Db_Table_RawClicks::CHANNEL);
		$this->addDataColumn(Pap_Db_Table_RawClicks::DATA1, "rc.".Pap_Db_Table_RawClicks::DATA1);
		$this->addDataColumn(Pap_Db_Table_RawClicks::DATA2, "rc.".Pap_Db_Table_RawClicks::DATA2);
		Gpf_Plugins_Engine::extensionPoint('PostAffiliate.ClicksGrid.initDataColumns', $this);
	}

	protected function initDefaultView() {
		$this->addDefaultViewColumn('affiliate', '40px');
		$this->addDefaultViewColumn('banner', '40px');
		$this->addDefaultViewColumn('campaign', '40px');
		$this->addDefaultViewColumn(Pap_Db_Table_RawClicks::COUNTRYCODE, '40px');
		$this->addDefaultViewColumn(Pap_Db_Table_RawClicks::RTYPE, '40px');
		$this->addDefaultViewColumn(Pap_Db_Table_RawClicks::DATETIME, '40px', 'D');
		$this->addDefaultViewColumn(Pap_Db_Table_RawClicks::REFERERURL, '40px');
		$this->addDefaultViewColumn(Pap_Db_Table_RawClicks::IP, '40px');
		$this->addDefaultViewColumn(Pap_Db_Table_RawClicks::DATA1, '40px');
		$this->addDefaultViewColumn(Pap_Db_Table_RawClicks::DATA2, '40px');
	}

	protected function buildFrom() {
		$this->_selectBuilder->from->addSubselect($this->rawClicksSelect, "rc");
		$onCondition = "rc.".Pap_Db_Table_RawClicks::CAMPAIGNID." = c.".Pap_Db_Table_Campaigns::ID;
		$this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c', $onCondition);
		$this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Banners::getName(), 'b', "rc.bannerid = b.bannerid");
		$this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Banners::getName(), 'pb', "rc.parentbannerid = pb.bannerid");
		$this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "rc.userid = pu.userid");
		$this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
		$this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
		$this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Channels::getName(), 'ch', 'ch.channelid=rc.channel');
	}

    /**
     * @param $params
     * @return Gpf_View_GridService_IdsIterator
     */
    public function getIdsIterator(Gpf_Rpc_Params $params) {
    	$this->initRawClicksSelect();
        return parent::getIdsIterator($params);
    }

	protected function initRawClicksSelect() {
		$this->rawClicksSelect = new Gpf_SqlBuilder_SelectBuilder();
		$this->rawClicksSelect->select->add('subrc.*');
		$this->rawClicksSelect->from->add(Pap_Db_Table_RawClicks::getName(), 'subrc');
		Gpf_Plugins_Engine::extensionPoint('PostAffiliate.ClicksGrid.initRawClicksSelect', $this->rawClicksSelect);
	}

	protected function computeCount() {
		$countSelect = new Gpf_SqlBuilder_SelectBuilder();
		$countSelect->cloneObj($this->rawClicksSelect);
		$countSelect->select = new Gpf_SqlBuilder_SelectClause();
		$countSelect->select->add('count(*)', 'count');
		$countSelect->orderBy = new Gpf_SqlBuilder_OrderByClause();
		$this->_count = $countSelect->getOneRow()->get('count');
	}

	protected function createResultSelect() {
		$this->initRawClicksSelect();
		parent::createResultSelect();
	}

	protected function buildFilter() {
		foreach ($this->filters as $filter) {
			if (array_key_exists($filter->getCode(), $this->dataColumns)) {
				$dataColumn = $this->dataColumns[$filter->getCode()];
				if ($dataColumn->getName() == 'rc.accountid') {
					$filter->setCode(str_replace('rc.', 'subc.', $dataColumn->getName()));
					$filter->addTo($this->rawClicksSelect->where);
					continue;
				}
				$filter->setCode(str_replace('rc.', 'subrc.', $dataColumn->getName()));
				$filter->addTo($this->rawClicksSelect->where);
			} else {
				$this->addFilter($filter);
			}
		}
	}

	protected function buildOrder() {
		if ($this->_sortColumn) {
			if (array_key_exists($this->_sortColumn, $this->dataColumns)) {
				if ($this->_sortColumn !== 'channelname') {
					$this->rawClicksSelect->orderBy->add($this->_sortColumn, $this->_sortAsc);
				}
				$this->_selectBuilder->orderBy->add($this->_sortColumn, $this->_sortAsc);
			}
		}
	}

	protected function buildLimit() {
		$this->initLimit();
		while($this->offset > $this->_count) {
			$this->offset = $this->offset - $this->limit;
		}
		$this->rawClicksSelect->limit->set($this->offset, $this->limit);
	}

	protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
		switch ($filter->getCode()) {
			case "search":
				$this->addSearch($filter);
				break;
		}
	}

	private function addSearch(Gpf_SqlBuilder_Filter $filter) {
		$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('subrc.'.Pap_Db_Table_RawClicks::ID, 'LIKE', '%'.$filter->getValue().'%', 'OR');
		$condition->add('subrc.'.Pap_Db_Table_RawClicks::DATA1, 'LIKE', '%'.$filter->getValue().'%', 'OR');
		$condition->add('subrc.'.Pap_Db_Table_RawClicks::DATA2, 'LIKE', '%'.$filter->getValue().'%', 'OR');
		$condition->add('subrc.'.Pap_Db_Table_RawClicks::IP, 'LIKE', '%'.$filter->getValue().'%', 'OR');

		$this->rawClicksSelect->where->addCondition($condition);
	}

	/**
	 * @service click read
	 * @param Gpf_Rpc_Params $params
	 */
	public function getCustomFilterFields(Gpf_Rpc_Params $params) {
		$filterFields = new Gpf_View_CustomFilterFields();
		$filterFields->addStringField(Pap_Db_Table_RawClicks::IP, $this->_("Ip"));
		$filterFields->addStringField(Pap_Db_Table_RawClicks::DATA1, $this->_("Extra data 1"));
		$filterFields->addStringField(Pap_Db_Table_RawClicks::DATA2, $this->_("Extra data 2"));
		$filterFields->addStringField(Pap_Db_Table_RawClicks::REFERERURL, $this->_("Referrer"));
		$filterFields->addStringField(Pap_Db_Table_RawClicks::COUNTRYCODE, $this->_("Country code"));

		return $filterFields->getRecordSet();
	}

	/**
	 * @service click read
	 * @return Gpf_Rpc_Serializable
	 */
	public function getRows(Gpf_Rpc_Params $params) {
		return parent::getRows($params);
	}

	/**
	 * @service click export
	 * @return Gpf_Rpc_Serializable
	 */
	public function getCSVFile(Gpf_Rpc_Params $params) {
		return parent::getCSVFile($params);
	}
}
?>
