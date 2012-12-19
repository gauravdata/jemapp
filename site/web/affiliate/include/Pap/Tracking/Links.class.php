<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: ImpressionTracker.class.php 18149 2008-05-22 14:31:02Z mfric $
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
class Pap_Tracking_Links extends Gpf_Object {

    /**
     * Returns general affiliate link
     *
     * @service general_link read
     */
    public function getGeneralAffiliateLink(Gpf_Rpc_Params $params) {
        $response = new Gpf_Rpc_Data($params);
        $response->setValue("generalAffiliateLink",
            $this->getGeneralAffiliateLinkNoRpc($response->getId()));
        return $response;
    }
    
    public function getGeneralAffiliateLinkNoRpc($affiliateId = null) {
        if ($affiliateId == null) {
            $affiliateId = Gpf_Session::getAuthUser()->getUserId();
        }
        $affiliate = new Pap_Common_User();
        $affiliate->setId($affiliateId);
        $affiliate->load();
        $mainSiteUrl = Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL);
        return Pap_Tracking_ClickTracker::getInstance()->getClickUrl(null, $affiliate, $mainSiteUrl);
    }
}

?>
