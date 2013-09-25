<?php
/**
 * BusinessCatalyst Notification request
 */

require_once '../../scripts/bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());

$bc = new BusinessCatalyst_RetrieveOrders();
$bc->retrieve(); 

?>
