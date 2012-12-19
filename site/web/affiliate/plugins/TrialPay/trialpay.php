<?php
/**
 * Processes TrialPay request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
    
$tracker = TrialPay_Tracker::getInstance();
$tracker->process();
?>
