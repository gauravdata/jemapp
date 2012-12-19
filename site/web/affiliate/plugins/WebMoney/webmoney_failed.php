<?php
/**
 * Processes WebMoney Failed request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
    
$tracker = WebMoney_Tracker::getInstance();
$tracker->finishTransaction(Pap_Common_Constants::STATUS_DECLINED);
?>
