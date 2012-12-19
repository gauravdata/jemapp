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
 */
class Pap_Tracking_BannerViewerRequest extends Gpf_Object {
	/**
	 * @var Pap_Common_Banner_Viewer
	 */
	private $viewer;

	private $bannerType;
	
	function __construct($bannerType){
		$this->bannerType = $bannerType;
	}

	function getType(){
		return $this->bannerType;
	}
	
	function setViewer(Pap_Common_Banner_Viewer $viewer){
		$this->viewer = $viewer;
	}

	/**
	 * @return Pap_Common_Banner_Viewer
	 */
	function getViewer(){
		return $this->viewer;
	}
}

?>
