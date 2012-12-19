<?php
/**
 * Processes PayPal request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = StormPay_Tracker::getInstance();
$tracker->process();
header("HTTP/1.1 202 Accepted");
?>
