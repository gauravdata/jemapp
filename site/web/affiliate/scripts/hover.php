<?php
/**
 * Hover banner
 */

require_once 'bootstrap.php';

Gpf_Session::create(new Pap_Tracking_ModuleBase());
$bannerViewer = new Pap_Tracking_BannerViewer();
echo $bannerViewer->showHover();

?>
