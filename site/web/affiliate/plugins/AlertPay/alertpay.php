<?php
/**
 * Processes AlertPay request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = AlertPay_Tracker::getInstance();
$tracker->process();
?>
