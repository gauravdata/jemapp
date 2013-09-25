<?php
/**
 * Processes 2Checkout request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = TwoCheckout_Tracker::getInstance();
$tracker->process();
?>
