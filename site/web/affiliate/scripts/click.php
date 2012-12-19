<?php
require_once('lib/tracking.php');

$settings = new Lib_SettingFile();
$settings->load();

$destUrl = @$_GET[$settings->get('param_name_dest_url')];

if ($destUrl == '') {
    $bannerId = @$_GET[$settings->get('param_name_banner_id')];
    $db = $settings->getDb();
    if($bannerId != '') {
        $result = $db->query("SELECT destinationurl FROM qu_pap_banners WHERE bannerid='".$db->escape($bannerId)."'");
        if ($result) {
            if ($row = mysql_fetch_assoc($result)) {
                $destUrl = $row['destinationurl'];
            }
        }
    }
}
if ($destUrl == '') {
    $result = $db->query('SELECT value FROM qu_g_settings WHERE name="mainSiteUrl"');
    if ($result) {
        if ($row = mysql_fetch_assoc($result)) {
            $destUrl = $row['value'];
        }
    }
}

require_once 'bootstrap.php';
@include_once('../include/Compiled/Core.php');

Gpf_Session::create(new Pap_Tracking_ModuleBase(), null, false);

$banner = new Pap_Common_Banner();
$user = new Pap_Affiliates_User();
$userId = @$_GET[$settings->get('param_name_user_id')];

try {
    $user = $user->loadFromId($userId);
    $destUrl = $banner->replaceUserConstants($destUrl, $user, 'Y');
} catch (Gpf_Exception $e) {
    $destUrl = $banner->removeUserConstants($destUrl, 'Y');
}
$clickFieldsValues = array();
$clickFieldsValues['chan'] = @$_GET['chan'];
$clickFieldsValues['extra_data1'] = @$_GET[$settings->get('param_name_extra_data1')];
$clickFieldsValues['extra_data2'] = @$_GET[$settings->get('param_name_extra_data2')];

$destUrl = $banner->replaceClickConstants($destUrl, $clickFieldsValues);

@header('Location: ' . $destUrl, true, 301);
$getParams = '?';
foreach ($_GET as $name => $value) {
    $getParams .= $name.'='.urlencode($value).'&';
}

$params = new Lib_VisitParams();
$params->setReferrerUrl(Lib_Server::getReferer());
$params->setTrackMethod('N');
$params->setGet(rtrim($getParams, '&?'));
$params->setCookies(Lib_VisitorCookie::readOldCookies());
$params->setIp(Lib_Server::getRemoteIp());
$params->setUserAgent(Lib_Server::getUserAgent());
$params->setAccountid(@$_GET['accountId']);
$params->setVisitorId(@$_GET['visitorId']);

Lib_VisitorCookie::readVisitorIdAndAccountId($params, $settings, false);
if ($settings->isOfflineVisitProcessing()) {
    $settings->saveVisit($params, $settings->get('visitsTableInput'));
    return;
}




$visit = new Pap_Db_Visit(0);
foreach ($params->toArray() as $key => $value) {
    $visit->set($key, $value);
}
$processor = new Pap_Tracking_Visit_Processor();
$processor->runOnline($visit);

?>
