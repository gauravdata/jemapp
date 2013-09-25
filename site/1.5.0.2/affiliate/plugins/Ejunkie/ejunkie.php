<?php
/**
 * Processes E-junkie request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
	
$tracker = Ejunkie_Tracker::getInstance();
$tracker->process();
?>
