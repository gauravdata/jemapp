<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CampaignStatisticsData.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Affiliates_Reports_TrendsReportActionWidget extends Pap_Merchants_Reports_TrendsReportActionWidget {
    
    /**
     * @return Pap_Stats_Params
     */
    protected function createStatParams(Gpf_Rpc_FilterCollection $filters, $datePreset = null) {
        $statsParams = parent::createStatParams($filters, $datePreset);
        $statsParams->setAffiliateId(Gpf_Session::getAuthUser()->getPapUserId());
        $statsParams->setChannel($filters->getFilterValue('channel'));
        return $statsParams;
    }
}

?>
