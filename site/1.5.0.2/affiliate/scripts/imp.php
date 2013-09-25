<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak, Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

require_once('lib/fast_init.php');

function output($bannerId) {
	@setcookie('PAPCookie_Imp_'.$bannerId, 'pap', time() + 315569260);
	if (@$_GET['PDebug'] != 'Y') {
	   @header('Content-Type: image/gif', true, null);
	   readfile('pix.gif');
	}
}

$settings = new Lib_SettingFile();
$settings->load();

$bannerId = @$_GET[$settings->get(Lib_SettingFile::PARAM_NAME_BANNER_ID)];

$params = new Lib_ImpParams();
$params->setDate(date('Y-m-d H:i:s'));
$params->setRtype(@$_COOKIE['PAPCookie_Imp_'.$bannerId] == '' ? 'U' : 'R');
$params->setUserid(@$_GET[$settings->get(Lib_SettingFile::PARAM_NAME_USER_ID)]);
$params->setBannerid($bannerId);
$params->setParentbannerid(@$_GET[$settings->get(Lib_SettingFile::PARAM_NAME_ROTATOR_ID)]);
$params->setChannel(@$_GET['chan']);
$params->setIp(Lib_Server::getRemoteIp());
$params->setData1(@$_GET[$settings->get('param_name_extra_data1')]);
$params->setData2(@$_GET[$settings->get('param_name_extra_data2')]);

if ($settings->isOfflineImpressionProcessing()) {
    $settings->getDb()->saveToDb($params, 'qu_pap_impressions'. $settings->get('impTableInput'));
	output($bannerId);
	return;
}
require_once 'bootstrap.php';
@include_once('../include/Compiled/Impression.php');

Gpf_Session::create(new Pap_Tracking_ModuleBase(), null, false);

$impression = new Pap_Db_RawImpression(0);
foreach ($params->toArray() as $key => $value) {
	$impression->set($key, $value);
}
$processor = new Pap_Tracking_Impression_ImpressionProcessor();
$processor->runOnline($impression);

output($bannerId);

?>
