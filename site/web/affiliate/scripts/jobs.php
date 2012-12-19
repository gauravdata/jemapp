<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: importLanguage.php 13163 2007-08-07 11:15:49Z aharsani $
*
*   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
*   Version 1.0 (the "License"); you may not use this file except in compliance
*   with the License. You may obtain a copy of the License at
*   http://www.qualityunit.com/licenses/license
*
*/
ob_start();

chdir(dirname(__FILE__));
require_once 'bootstrap.php';

$inclusion_type = null;
$inclusion_tasks = array();

if (isset($argv) && count($argv) >= 3) {
	if ($argv[1] == '--include') {
	   $inclusion_type = Gpf_Tasks_Runner::INCLUDE_TASKS;
	} else if ($argv[1] == '--exclude') {
	   $inclusion_type = Gpf_Tasks_Runner::EXCLUDE_TASKS;
	}	
	foreach ($argv as $key => $argument) {
		if ($key > 1) $inclusion_tasks[] = $argument;
	}
}

Gpf_Session::create(new Pap_Tracking_ModuleBase(), null, false);
$taskRunner = new Gpf_Tasks_Runner();

if (isset($_REQUEST['time'])) {
    $timeFrame = $_REQUEST['time'];
} else {
    sleep(rand(0,30)); //Sleep random number of seconds until will be processing started
    if ($inclusion_type !== null) {
        $timeFrame = Gpf_Settings::get(Gpf_Settings_Gpf::CRON_RUN_INTERVAL)*60 - 20;    
    } else {
        $timeFrame = 50;
    }
}

$taskRunner->run($timeFrame, $inclusion_type, $inclusion_tasks);

$content = ob_get_contents();
ob_end_clean();
echo trim($content);
?>
