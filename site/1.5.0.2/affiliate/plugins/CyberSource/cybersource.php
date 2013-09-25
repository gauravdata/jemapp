<?php
/**
 * Processes Netbilling request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
    
$tracker = CyberSource_Tracker::getInstance();
$tracker->process();
?>
