<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 * TODO: not checked yet, adopted from old PAP4
 */
class Pap_Common_Banner_PopUpUnder extends Pap_Common_Banner {
	var $bannerFactory;

	var $popUrl 		= 1;
	var $popBanner 	= 2;
	
	var $windowSizePredefined = 1;
	var $windowSizeOwn 		= 2;
	
	var $popType;
	var $windowSizeType;
	var $windowWidth;
	var $windowHeight;
	var $windowResizable;
	var $windowStatus;
	var $windowToolbar;
	var $windowLocation;
	var $windowDirectories;
	var $windowMenubar;
	var $windowScrollbars;
    
	function Pap_Banners_BannerPopUpUnder() {
		$this->db = $this->createDatabase();
		$this->bannerFactory = Gpf::newObj('Pap_Banners_BannerFactory');
	}

	protected function getBannerCode(Pap_Common_User $user, $includeImpressionTracking = true, $isPreview = false) {
		$this->parseBannerDescription();
		
		$code = '<script type="text/javascript">'.
        		'var TheNewWindow = window.open("'.$this->me->getBannerPopupPopunderUrl($user).'",\'ThePop\','.
		        '\'top=0,left=0,width='.$this->windowWidth.',height='.$this->windowHeight.
        		',toolbar='.$this->windowToolbar.',location='.$banner_details['window_location'].
        		',directories='.$this->windowDirectories.',status='.$this->windowStatus.
        		',menubar='.$this->windowMenubar.',scrollbars='.$this->windowScrollbars.
        		',resizable='.$this->windowResizable.'\');';
        if ($this->bannertype == $this->bannerFactory->bannerTypePopup) {
        	$code .= ' TheNewWindow.focus();';
        } else {
        	$code .= ' TheNewWindow.blur();';
        }
        $code .= '</script>';

        return $code;
	}
	
	/**
	 * Returns url for popup/popunder
	 */
	function getBannerPopupPopunderUrl($user) {
		$url = Gpf_Paths::getInstance()->getFullScriptsUrl();
		if ($this->popType == $this->popBanner) {
			$banner = $this->bannerFactory->getBanner($this->sourceurl);
			//$banner->setOtherBannerIdForClickUrl($this->getId());
			$url .= "popBanner.php".
					"?bannerContent=".urlencode($banner->getBannerCode($user, $this->getId()));
					
		} else {
			$url .= "popUrl.php".
					"?bannerUrl=".urlencode($this->sourceurl);
					"&clickUrl=".urlencode($this->getClickUrl($user));
		}
		$url .= "&impressionCode=".urlencode($this->getTrackingCode($user));
		return $url;
	}
	
	function parseBannerDescription() {
        $descArray = explode('_', $this->description);
        $this->popType = $descArray[0];
        $this->windowSizeType = $descArray[1];
        $this->windowWidth = $descArray[2];
        $this->windowHeight = $descArray[3];
        
        /* TODO Find out why this was in old code - seems useless
        if ($this->windowSizeType == self::$windowSizePredefined) {
			$this->windowSizeType = $descArray[2].'_'.$descArray[3];
        }
		*/

        $this->windowResizable = $descArray[4];
        $this->windowStatus = $descArray[5];
        $this->windowToolbar = $descArray[6];
        $this->windowLocation = $descArray[7];
        $this->windowDirectories = $descArray[8];
        $this->windowMenubar = $descArray[9];
        $this->windowScrollbars = $descArray[10];
    }
    
	function getCode($user) {
		return $this->getBannerCode($user);
	}
}

?>
