<?php
/**
 * Processes GoogleCheckout request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = PremiumWebCart_Tracker::getInstance();
$tracker->process();
?>
