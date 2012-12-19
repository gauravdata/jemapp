<?php
/**
 * Processes WebMoney request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
    
$tracker = WebMoney_Tracker::getInstance();
$tracker->process();
?>
