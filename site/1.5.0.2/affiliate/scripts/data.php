<?php
/**
 * Server file for pap alert
 */

require_once 'bootstrap.php';

$userName = addslashes($_REQUEST['u']);
$password = addslashes($_REQUEST['p']);


$papAlert = new Pap_Alert_Data($userName, $password);
$papAlert->getNotification();
?>
