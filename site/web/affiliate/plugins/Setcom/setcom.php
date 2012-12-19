<?php
/**
 * Processes Setcom request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = Setcom_Tracker::getInstance();
$tracker->process();
?>
