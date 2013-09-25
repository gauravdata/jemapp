<?php
/**
 * Processes Netbilling request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
    
$tracker = Netbilling_Tracker::getInstance();
$tracker->process();
?>
