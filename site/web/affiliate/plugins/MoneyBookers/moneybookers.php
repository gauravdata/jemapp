<?php
/**
 * Processes MoneyBookers request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
    
$tracker = MoneyBookers_Tracker::getInstance();
$tracker->process();
?>
