<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Merchants_User_TopAffiliatesGrid extends Pap_Merchants_User_AffiliatesGrid {

	protected function initDefaultView() {
		$this->addDefaultViewColumn('name', 120);
		$this->addDefaultViewColumn('salesCount', 40);
		$this->addDefaultViewColumn('commissions', 40);
		$this->addDefaultViewColumn('clicksRaw', 40);
		$this->addDefaultViewColumn('impressionsRaw', 40);
	}

	protected function addActionViewColumn() {
	}

    protected function modifyResultSelect() {
    }

	/**
	 * @return Pap_Stats_Params
	 */
	protected function getStatsParameters() {
		$statsParams = parent::getStatsParameters();

		if (count($campaignFilter = $this->filters->getFilter('campaignid')) == 1) {
			$statsParams->setCampaignId($campaignFilter[0]->getValue());
		}
		if (count($transactionStatusFilter = $this->filters->getFilter('transactionstatus')) == 1) {
			$statsParams->setStatus($transactionStatusFilter[0]->getValue());
		}
		return $this->addParamsWithDateRangeFilter($statsParams);
	}

	protected function doMossoHack(Gpf_DbEngine_Table $primaryTable, $primaryTableAlias, $primaryColumnName) {
	    //optimalisation to prevent multiple joins with stats subselects	    
	}    
}

?>
