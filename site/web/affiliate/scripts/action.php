<?php
/**
 * Register sale/lead/action
 *
 * Sale tracking is made completely using plugins, every action is performed by a plugin
 */

require_once 'bootstrap.php';

Gpf_Session::create(new Pap_Merchant(), null, false);
	
$request = new Pap_Tracking_ActionRequest();
$tracker = Pap_Tracking_ActionTracker::getInstance();
$tracker->setAccountId($request->getAccountId());
$tracker->setRefererUrl($request->getRefererUrl());
$tracker->setVisitorId($request->getVisitorId());
$tracker->setTrackMethod(Pap_Common_Transaction::TRACKING_METHOD_3RDPARTY_COOKIE);

$action = $tracker->createAction($request->getRawActionCode());
$action->setTotalCost($request->getRawTotalCost());
$action->setOrderId($request->getRawOrderId());
$action->setProductId($request->getRawProductId());
for ($i=1; $i<=5; $i++) {
    $action->setData($i, $request->getRawExtraData($i));
}
$action->setAffiliateId($request->getRawAffiliateId());
$action->setCampaignId($request->getRawCampaignId());
$action->setCustomCommission($request->getRawCustomCommission());
$action->setStatus($request->getRawCustomStatus());
$action->setCouponCode($request->getRawCoupon());
$action->setChannelId($request->getChannelId());
$action->setCurrency($request->getCurrency());

$tracker->track();
?>
