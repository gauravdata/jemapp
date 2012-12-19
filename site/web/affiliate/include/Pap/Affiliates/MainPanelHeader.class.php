<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Affiliates_MainPanelHeader extends Gpf_Object {


    public static function getAffiliateLink() {
        $mainSiteUrl = Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL);
        $user = new Pap_Affiliates_User();
        $user->setId(Gpf_Session::getAuthUser()->getPapUserId());
        $user->load();
        if (Pap_Tracking_ClickTracker::getInstance()->getLinkingMethod() == Pap_Tracking_ClickTracker::LINKMETHOD_REDIRECT) {
            $affiliateLink = "";
        } elseif(Pap_Tracking_ClickTracker::getInstance()->getLinkingMethod() == Pap_Tracking_ClickTracker::LINKMETHOD_ANCHOR
               && Gpf_Settings::get(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING) == GPF::YES) {
            $affiliateLink = $mainSiteUrl . "#" . $user->getRefId();
        } else {
            $affiliateLink = Pap_Tracking_ClickTracker::getInstance()->getClickUrl(null, $user, $mainSiteUrl);
        }
        return $affiliateLink;
    }

    /**
     * @service period_stats read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $transactions = new Pap_Stats_Transactions(new Pap_Stats_Params());
        $data->setValue('totalCommisonsApprovedUnpaid', $transactions->getCommission()->getApproved());
        $data->setValue('totalCommissionsPending', $transactions->getCommission()->getPending());
        $data->setValue('totalCommissionsPaid', $transactions->getCommission()->getPaid());
        $data->setValue("generalAffiliateLink", self::getAffiliateLink());

        return $data;
    }
}

?>
