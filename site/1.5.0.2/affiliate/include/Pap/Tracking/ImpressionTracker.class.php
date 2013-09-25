<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: ImpressionTracker.class.php 25729 2009-10-16 12:27:45Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Tracking_ImpressionTracker extends Pap_Tracking_TrackerBase {
    /**
     * @var Pap_Tracking_ImpressionTracker
     */
    private static $instance = NULL;

    /**
     * @return Pap_Tracking_ImpressionTracker
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Pap_Tracking_ImpressionTracker();
        }
        return self::$instance;
    }

    private function __construct() {
    }

    /**
     * @param Pap_Common_Banner $banner
     * @param Pap_Common_User $user
     * @param Pap_Db_Channel $channel
     * @return string
     */
    public function getImpressionTrackingCode(Pap_Common_Banner $banner, Pap_Common_User $user, Pap_Db_Channel $channel = null) {
        $code  = "<img style=\"border:0\" src=\"";
        $code .= $this->getSrcCode($banner,$user,$channel);
        $code .= "\" width=\"1\" height=\"1\" alt=\"\" />";
        return $code;
    }

    public function getSrcCode(Pap_Common_Banner $banner, Pap_Common_User $user, Pap_Db_Channel $channel = null){
        $code = $this->getScriptUrl("imp.php");
        $code .= "?".Pap_Tracking_Request::getAffiliateClickParamName()."=".$user->getRefId();
        $code .= "&amp;".Pap_Tracking_Request::getBannerClickParamName()."=".$banner->getId();
        if ($banner->getParentBannerId() != null) {
            $code .= "&amp;".Pap_Tracking_Request::getRotatorBannerParamName()."=".$banner->getParentBannerId();
        }
        if($channel != null && is_object($channel)) {
            $code .= "&amp;".Pap_Tracking_Request::getChannelParamName()."=".$channel->getValue();
        }
        return $code;
    }
     
}

?>
