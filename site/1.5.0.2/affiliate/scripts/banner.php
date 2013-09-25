<?php
require_once 'lib/banner_viewer.php';

$settings = new Lib_SettingFile();
$settings->load();

$bannerViewer = new Lib_BannerViewer($settings);

if ($bannerViewer->displayBanner()) {
    exit;
}

require_once 'bootstrap.php';
@include_once('../include/Compiled/Core.php');
@include_once('../include/Compiled/Banner.php');

Gpf_Session::create(new Pap_Tracking_ModuleBase(), null, false);
$bannerViewerNormal = new Pap_Tracking_BannerViewer();

$cachedBanner = new Pap_Db_CachedBanner();
foreach ($bannerViewer->getBannerParams() as $key => $value) {
    $cachedBanner->set($key, $value);
}
$bannerViewerNormal->show($cachedBanner);

?>
