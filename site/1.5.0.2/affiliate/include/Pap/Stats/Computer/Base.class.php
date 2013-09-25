<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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
abstract class Pap_Stats_Computer_Base extends Gpf_Object {

	/**
	 * @var Pap_Stats_Table
	 */
	private $table;

	/**
	 * @var Pap_Stats_Params
	 */
	protected $params;

	/**
	 * @var Gpf_SqlBuilder_SelectBuilder
	 */
	protected $selectBuilder;

	public function __construct(Pap_Stats_Table $table, Pap_Stats_Params $params) {
		$this->table = $table;
		$this->params = $params;
	}

	public final function computeStats() {
		$this->initSelectBuilder();
		$this->processResult();
	}

	protected abstract function processResult();

	protected abstract function initSelectClause();

	protected function initGroupBy() {
	}

	protected function initFrom() {
		$this->selectBuilder->from->add($this->table->name(), 't');
	}

	protected function initWhereConditions() {
		$this->params->addTo($this->selectBuilder, 't');
	}

	private function initSelectBuilder() {
		$this->selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
		$this->initSelectClause();
		$this->initFrom();
		$this->initWhereConditions();
		$this->initGroupBy();
		
		$selectBuilderCompoundContext = new Pap_Common_Reports_SelectBuilderCompoundParams($this->selectBuilder, $this->params);
		
		Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Stats_Computer_Base.initSelectBuilder', $selectBuilderCompoundContext);
	}
}
?>
