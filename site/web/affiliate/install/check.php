<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @since Version 4.0.0
 *   $Id: index.php 13370 2007-08-27 12:41:15Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */
if(version_compare(phpversion(), '5.2.0') < 0) {
    die('Please upgrade to PHP 5.2.0 or higher. Your current PHP version is ' . phpversion());
}

require_once '../scripts/bootstrap.php';

Gpf_Templates_Smarty::checkCompileDirRequirementsInInstallMode();

$integrityCheck = new Pap_IntegrityCheck(true);
$integrityCheck->check();

?>
