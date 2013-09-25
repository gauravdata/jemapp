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
class GeoIp_Driver extends Gpf_Object {
    
    /**
     * @var GeoIp_Driver
     */
    private static $instance;
    
    /**
     * Return instance of first active driver
     * 
     * @return GeoIp_Driver
     */
    public static function getInstance() {
        if (self::$instance === null) {
            $fileDb = new GeoIp_Driver_File();
            if ($fileDb->isActive()) {
                self::$instance = $fileDb;
                return self::$instance;
            }
            
            throw new Gpf_Exception('No GeoIp driver available');
        }
        return self::$instance;
    }

    /**
     * Check if driver is active. Each driver should implement this function
     *
     * @return boolean
     */
    public function isActive() {
        return false;
    }
    
    /**
     * Load into Location object all available data
     *
     * @param GeoIp_Location $location
     */
    public function loadLocation(GeoIp_Location $location) {
        throw new Gpf_Exception('Not implemented');
    }
}
?>
