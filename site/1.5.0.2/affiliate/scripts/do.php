<?php
/**
 * Perform quick task
 */

require_once 'bootstrap.php';
Gpf_Session::create();

$quickTaskRunner = new Gpf_Tasks_QuickTaskRunner();
$quickTaskRunner->executeTask();
?>
