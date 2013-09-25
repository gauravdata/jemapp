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
class GeoIp_Driver_File extends GeoIp_Driver {

    const GEOIP_FILE = 'GeoLiteCity.dat';

    /**
     * @var Gpf_Io_File
     */
    private $file;

    public function __construct() {
        $this->file = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountPath() . self::GEOIP_FILE);
        if (!$this->file->isExists()) {
            $this->file = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountsPath() . self::GEOIP_FILE);
            if (!$this->file->isExists()) {
                $this->file = new Gpf_Io_File(Gpf_Paths::getInstance()->getFrameworkPath() . 'plugins/GeoIp/' . self::GEOIP_FILE);
            }
        }
    }

    public function isActive() {
        return $this->file->isExists();
    }

    /**
     * Load into Location object all available data
     *
     * @param GeoIp_Location $location
     */
    public function loadLocation(GeoIp_Location $location) {
        require_once "Net/GeoIP.php";
        $flag = Net_GeoIP::STANDARD;
//TODO: on some hostings it failed to work (e.g. mosso.com), so we will disable option of shared memory until it will be solved
//        if (Gpf_Php::isFunctionEnabled('shmop_open')) {
//            $flag = Net_GeoIP::SHARED_MEMORY;
//        }

        $geoip = Net_GeoIP::getInstance($this->file->getFileName(), $flag);
        if ($geoipLocation = @$geoip->lookupLocation($location->getIpString())) {
            $location->setCountryCode($geoipLocation->countryCode);
            $location->setCity($geoipLocation->city);
            $location->setAreaCode($geoipLocation->areaCode);
            $location->setCountryName($geoipLocation->countryName);
            $location->setDmaCode($geoipLocation->dmaCode);
            $location->setLatitude($geoipLocation->latitude);
            $location->setLongitude($geoipLocation->longitude);
            $location->setPostalCode($geoipLocation->postalCode);
            $location->setRegion($geoipLocation->region);
        } else {
            throw new Gpf_Exception($this->_('Ip address %s is not in geoip database.', $location->getIpString()));
        }
    }

}
?>
