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
class Pap_Common_Banner_BannerRequest {
    /**
     * @var string
     */
    var $bannerType;
    /**
     * @var string
     */
    var $banner;
     
    /**
     * @param  string
     */
    function __construct($bannerType){
        $this->bannerType = $bannerType;
    }

    /**
     * @return Pap_Common_Banner
     */
    function getBanner(){
        return $this->banner;
    }


    function setBanner(Pap_Common_Banner $banner){
        return $this->banner = $banner;
    }

    /**
     * @return string
     */
    function getType(){
        return $this->bannerType;
    }
}

?>
