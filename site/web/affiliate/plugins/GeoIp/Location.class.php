<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Google.class.php 18112 2008-05-20 07:17:10Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class GeoIp_Location extends Gpf_Object {

    /**
     * Ip address in long number
     *
     * @var long number
     */
    private $ip;

    private $countryCode;

    private $city;

    private $areaCode;
    
    private $countryName;
    
    private $dmaCode;
    
    private $latitude;
    
    private $longitude;
    
    private $postalCode;
    
    private $region;
    
    /**
     * Set ip address of location
     *
     * @param string $ip Ip location in form XXX.XXX.XXX.XXX
     */
    public function setIpString($ip) {
        $this->ip = ip2long($ip);
    }

    public function setIp($ip) {
        $this->ip = $ip;
    }

    public function getIp() {
        return $this->ip;
    }

    public function getIpString() {
        return long2ip($this->ip);
    }

    /**
     * @throws Gpf_Exception
     */
    public function load() {
        $geoipDriver = GeoIp_Driver::getInstance();
        $geoipDriver->loadLocation($this);
    }

    public function setCountryCode($countryCode) {
        $this->countryCode = $countryCode;
    }

    public function getCountryCode() {
        return $this->countryCode;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getCity() {
        return $this->city;
    }

    public function setAreaCode($areaCode) {
        $this->areaCode = $areaCode;
    }
    
    public function getAreaCode() {
        return $this->areaCode;
    }
    
    public function setCountryName($countryName) {
        $this->countryName = $countryName;
    }
    
    public function getCountryName() {
        return $this->countryName;
    }
    
    public function setDmaCode($dmaCode) {
        $this->dmaCode = $dmaCode;
    }
    
    public function getDmaCode() {
        return $this->dmaCode;
    }
    
    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }
    
    public function getLatitude() {
        return $this->latitude;
    }
    
    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }
    
    public function getLongitude() {
        return $this->longitude;
    }
    
    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }

    public function getPostalCode() {
        return $this->postalCode;
    }
    
    public function setRegion($region) {
        $this->region = $region;
    }

    public function getRegion() {
        return $this->region;
    }
}
?>
