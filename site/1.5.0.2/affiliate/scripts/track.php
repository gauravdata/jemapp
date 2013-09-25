<?php
require_once 'lib/tracking.php';
@include_once('custom.php');

if (@$_GET['PDebug'] != 'Y') {
    @header('Content-Type: application/x-javascript');
}

try {
    $REQUEST = array_merge($_GET, $_POST);
	$settings = new Lib_SettingFile();
	$settings->load();

	if($settings->get('bannedips_clicks_from_iframe') == 'Y' && @$_GET['isInIframe'] == 'true') {
	    return;
	}
	$params = new Lib_VisitParams();
	$params->setUrl(@$_GET['url']);
	$params->setReferrerUrl(@$_GET['referrer']);
	$params->setTrackMethod(@$_GET['tracking']);
	$params->setGet(@$_GET['getParams']);
	$params->setAnchor(@$_GET['anchor']);
    $sale = @$REQUEST['sale'];
    if (get_magic_quotes_gpc()) {
        $sale = stripslashes($sale);
    }
	$params->setSale($sale);
	$params->setCookies(Lib_VisitorCookie::readOldCookies(@$_GET['cookies']));
	$params->setIp(@$_GET['ip'] != '' ? $_GET['ip'] : Lib_Server::getRemoteIp());
	$params->setUserAgent(@$_GET['useragent'] != '' ? $_GET['useragent'] : @$_SERVER['HTTP_USER_AGENT']);
	$params->setVisitorId(@$_GET['visitorId']);
	$params->setAccountId(@$_GET['accountId']);

	Lib_VisitorCookie::readVisitorIdAndAccountId($params, $settings);
	
	if( $settings->isOfflineVisitProcessing() ){
	    $settings->saveVisit($params, $settings->get('visitsTableInput'));
	    return;
	}
	require_once 'bootstrap.php';
	@include_once('../include/Compiled/Tracking.php');

	Gpf_Session::create(new Pap_Tracking_ModuleBase(), null, false);

	$visit = new Pap_Db_Visit();
	foreach ($params->toArray() as $key => $value) {
		$visit->set($key, $value);
	}
	$processor = new Pap_Tracking_Visit_Processor();
	$processor->runOnline($visit);
} catch(Exception $e) {
	echo '//' . $e->getMessage() . "\n";
}
?>
