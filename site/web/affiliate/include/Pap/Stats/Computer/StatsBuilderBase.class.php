<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
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
abstract class Pap_Stats_Computer_StatsBuilderBase extends Gpf_Object {
		
	/**
	 * @var Gpf_SqlBuilder_SelectBuilder
	 */
	protected $statsSelect;
	/**
	 * @var Gpf_SqlBuilder_SelectBuilder
	 */
	protected $transactionsSelect;
	
	public function __construct(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias) {
		$this->init($statParams, $groupColumn, $groupColumnAlias);
	}
	
	/**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
	public function getStatsSelect() {
		return $this->statsSelect;
	}
	
    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getTransactionsSelect() {
        return $this->transactionsSelect;
    }
	
	/**
	 * @return Gpf_SqlBuilder_WhereClause
	 */
	public function getTransactionsWhereClause() {
		return $this->transactionsSelect->where;
	}
	
	protected function init(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias) {
		$this->initSelectBuilders();
		$this->buildSelect($groupColumn, $groupColumnAlias);
		$this->buildFrom();
		$this->buildWhere($statParams);
		$this->buildGroupBy($groupColumn);
	}
	
	protected abstract function buildSelect($groupColumn, $groupColumnAlias);
	
	protected abstract function buildFrom();
	
	protected abstract function buildWhere(Pap_Stats_Params $statParams);
	
	protected abstract function buildGroupBy($groupColumn);
	
	private function initSelectBuilders() {
		$this->statsSelect = new Gpf_SqlBuilder_SelectBuilder();
		$this->transactionsSelect = new Gpf_SqlBuilder_SelectBuilder();
	}
}
?>
