<?php
/**
 * Processes Bluepay request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = Bluepay_Tracker::getInstance();
$tracker->process();
?>
