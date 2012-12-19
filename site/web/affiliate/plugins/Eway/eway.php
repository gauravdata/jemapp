<?php
/**
 * Processes Eway request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = Eway_Tracker::getInstance();
$tracker->process();
?>
