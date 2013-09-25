<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * returns all active and not stopped campaigns
 * @package PostAffiliatePro
 */
class Pap_Features_PrivateCampaigns_PrivateCampaignForAffiliateRichListBox extends Pap_Common_Campaign_CampaignRichListBox {
	 
    protected function createSelectBuilder() {
    	$selectBuilder = parent::createSelectBuilder();
    	$selectBuilder->where->add('c.'.Pap_Db_Table_Campaigns::TYPE, 'IN', array(Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL, Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION));
        return $selectBuilder;
    }
}
?>
