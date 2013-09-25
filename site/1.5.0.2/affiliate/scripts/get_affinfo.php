<?php
try {
    if (!isset($_GET['visitorId'])) {
        return;
    }
    $visitorId = $_GET['visitorId'];

    require_once 'bootstrap.php';
    @include_once('../include/Compiled/Tracking.php');

    Gpf_Session::create(new Pap_Tracking_ModuleBase(), null, false);

    $processor = new Pap_Tracking_Visit_SingleVisitorProcessor($visitorId);
    $processor->processAllVisitorVisits();
    $visitorAffiliate = $processor->getCurrentVisitorAffiliate();
    if ($visitorAffiliate != null) {
        echo "try{setAffiliateInfo('".$visitorAffiliate->getUserId()."', '".$visitorAffiliate->getCampaignId()."');}catch(e){};\n";
    }
} catch(Exception $e) {
    echo '//' . $e->getMessage() . "\n";
}


?>
