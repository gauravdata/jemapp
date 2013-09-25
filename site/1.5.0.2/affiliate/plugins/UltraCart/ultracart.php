<?php
/**
 * Processes UltraCart request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
    
$tracker = UltraCart_Tracker::getInstance();
$tracker->process();
?>
