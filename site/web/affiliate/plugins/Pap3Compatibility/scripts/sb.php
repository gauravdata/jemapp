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

include('lib/fast_init.php');

$settings = new Lib_SettingFile();
$settings->load();

$bannerId = @$_GET[$settings->get('param_name_banner_id')];

$impParams = array(
'date' => date('Y-m-d H:i:s'),
'rtype' => (@$_COOKIE['PAPCookie_Imp_'.$bannerId] == '' ? 'U' : 'R'),
'userid' => @$_GET[$settings->get('param_name_user_id')],
'bannerid' => $bannerId,
'channel' => @$_GET['chan'],
'data1' => @$_GET[$settings->get('param_name_extra_data1')],
'data2' => @$_GET[$settings->get('param_name_extra_data2')],
);

require_once 'bootstrap.php';
@include_once('../include/Compiled/Impression.php');

Gpf_Session::create(new Pap_Tracking_ModuleBase(), null, false);

$impression = new Pap_Db_RawImpression(0);
foreach ($impParams as $key => $value) {
    $impression->set($key, $value);
}
$processor = new Pap_Tracking_Impression_ImpressionProcessor();
$processor->runOnline($impression);

try {
    $banner = $processor->getBanner($bannerId);
    if ($banner != null && $banner->getBannerType() == Pap_Common_Banner_Factory::BannerTypeImage) {
        Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::LOCATION, $banner->getImageUrl(), 301);
        exit();
    }
} catch (Gpf_Exception $e) {
}

setcookie('PAPCookie_Imp_'.$bannerId, 'pap', time() + 315569260);
header('Content-Type: image/gif', true, null);
readfile('scripts/pix.gif');

?>
