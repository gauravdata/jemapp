<?php
/**
 * Processes ccBill request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = ccBill_Tracker::getInstance();
$tracker->process();
?>
