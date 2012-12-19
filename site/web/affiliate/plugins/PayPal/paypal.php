<?php
/**
 * Processes PayPal request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = PayPal_Tracker::getInstance();
$tracker->process();
?>
