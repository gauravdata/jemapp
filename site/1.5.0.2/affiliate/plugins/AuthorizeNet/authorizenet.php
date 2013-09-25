<?php
/**
 * Processes Authorize.net request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
    
$tracker = AuthorizeNet_Tracker::getInstance();
$tracker->process();
?>
