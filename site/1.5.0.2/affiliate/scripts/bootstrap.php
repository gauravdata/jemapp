<?php
error_reporting(E_ALL);

@ini_set('session.gc_maxlifetime', 28800);
@ini_set('session.cookie_path', '/');
@ini_set('session.use_cookies', true);
@ini_set('magic_quotes_runtime', false);
@ini_set('magic_quotes_gpc', true);
@ini_set('session.use_trans_sid', false);
@ini_set('zend.ze1_compatibility_mode', false);

@set_time_limit(0);
define('DB_TABLE_PREFIX', "qu_");

@include_once('custom.php'); 
@include_once('../include/Compiled/Core.php');

require_once 'paths.php';

$requirements = new Pap_Install_Requirements();
$requirements->checkRuntime();
Gpf_Application::create(new Pap_Application());
?>
