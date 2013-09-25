<?php
/**
 * PAP3 compatibility script
 */
require_once 'bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());

$tracker = new Pap3Compatibility_PreSale();

$tracker->setCookiesToBeDeleted();
$tracker->finishSale();
?>
