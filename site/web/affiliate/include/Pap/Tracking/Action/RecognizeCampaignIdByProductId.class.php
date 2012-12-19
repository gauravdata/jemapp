<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Tracking_Action_RecognizeCampaignIdByProductId extends Gpf_Object {

    public function __construct() {
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function getMatchingCampaignsRecordSet($productId) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::ID);
        $selectBuilder->select->add(Pap_Db_Table_Campaigns::PRODUCT_ID);
        $selectBuilder->from->add(Pap_Db_Table_Campaigns::getName());
        $selectBuilder->where->add(Pap_Db_Table_Campaigns::PRODUCT_ID, 'like', '%'.$productId.'%');
        
        return $selectBuilder->getAllRows();
    }
    
    /**
     * @return string campaignId
     * @throws Gpf_Exception
     */
    public function recognizeCampaignId(Pap_Contexts_Tracking $context, $productId) {
        if($productId == '') {
            $context->debug('Empty product ID');
            throw new Gpf_Exception('Empty product ID');
        }
        
        $matchingCampaigns = $this->getMatchingCampaignsRecordSet($productId);
        
        switch ($matchingCampaigns->getSize()) {
            case 0:
            	$context->debug('No campaign matching product ID: '.$productId);
                throw new Gpf_Exception('No campaign matching product ID: '.$productId);
            case 1:
                foreach ($matchingCampaigns as $campaign) {
                    $campaignId = $campaign->get(Pap_Db_Table_Campaigns::ID);
                    $context->debug("Campaign was found for this Product ID. Campaign Id: ".$campaignId);
                    return $campaignId;
                }
            default:
                $context->debug("More campaigns matched product ID '.$productId.'. Finding correct campaign");
                return $this->findBestMatchingCampaignId($matchingCampaigns, $productId);
        }
    }
    
    private function findBestMatchingCampaignId(Gpf_Data_RecordSet $matchingCampaigns, $productId) {
        foreach ($matchingCampaigns as $campaign) {
            $campaignProductIds = explode(',', $campaign->get(Pap_Db_Table_Campaigns::PRODUCT_ID));
            if (in_array($productId, array_values($campaignProductIds))) {
                return $campaign->get(Pap_Db_Table_Campaigns::ID);
            }
        }
        throw new Gpf_Exception('No campaign matching product ID: '.$productId);
    }
}

?>
