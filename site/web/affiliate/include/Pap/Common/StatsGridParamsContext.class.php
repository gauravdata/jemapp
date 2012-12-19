<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Common_StatsGridParamsContext {

	/**
	 * @var Gpf_SqlBuilder_SelectBuilder
	 */
	private $select;
	/**
	 * @var Pap_Common_StatsGrid
	 */
	private $statsGrid;
	/**
	 * @var Pap_Stats_Params
	 */
	private $statsParams;

	public function __construct(Gpf_SqlBuilder_SelectBuilder $select, Pap_Common_StatsGrid $statsGrid, Pap_Stats_Params $statsParams) {
		$this->select = $select;
		$this->statsGrid = $statsGrid;
		$this->statsParams = $statsParams;
	}

	/**
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	public function getSelectBuilder() {
		return $this->select;
	}

	/**
	 * @return Pap_Common_StatsGrid
	 */
	public function getStatsGrid() {
		return $this->statsGrid;
	}

	/**
	 * @return Pap_Stats_Params
	 */
	public function getStatsParams() {
		return $this->statsParams;
	}
}
?>
