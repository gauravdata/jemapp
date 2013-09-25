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
class GeoIp_GeoService extends Gpf_Object {

    /**
     * Load Location information for Ip address specified in Id
     *
     * @anonym
     * @service geoip read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $location = new GeoIp_Location();
        $location->setIpString($data->getId());
        $location->load();

        $data->setValue('ip', $location->getIpString());
        $data->setValue('countryCode', $location->getCountryCode());
        $data->setValue('countryName', $location->getCountryName());
        $data->setValue('city', $location->getCity());
        $data->setValue('areaCode', $location->getAreaCode());
        $data->setValue('dmaCode', $location->getDmaCode());
        $data->setValue('latitude', $location->getLatitude());
        $data->setValue('longitude', $location->getLongitude());
        $data->setValue('postalCode', $location->getPostalCode());
        $data->setValue('region', $location->getRegion());

        return $data;
    }

}

?>
