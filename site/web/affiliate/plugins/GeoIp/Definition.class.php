<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class GeoIp_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'GeoIp';
        $this->name = 'GeoIp Core';
        $this->description = 'GeoIp Core module will translate every Ip address into Location (Country, City, Latitude and Longitude).
All other GeoIp functionality is based on this plugin.
If this plugin will not be enabled, other GeoIp plugins will not work too.
Please read more how to configure plugin correctly in section "More info".';
        $this->version = '1.0.0';
        $this->help = 'GeoIp plugin is based on free GeoIp database offered by MaxMind.<br/>
Plugin requires GeoIp binary database (filename GeoLiteCity.dat) saved in your plugin directory (server/plugins/GeoIp/). <br/>
<b>You can downlaod latest version of GeoIp database from here: <a href="http://www.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">GeoLiteCity</a>.</b><br/>
(Don\'t forget to extract downloaded file before copying to plugin directory)</br><hr>

Free GeoIp database has lower accuracy as paid version offered by MaxMind,
but for most of installations it will be enough,
because in most cases you are interested in country level information and
City level is not so important.
<hr>
If you want to have more accurate results of IP identification,
you will need to buy GeoCity library from <a href="https://www.maxmind.com/app/cart?rId=qualityunit&add_product_id=132" target="_blank">MaxMind</a> and
replace file GeoLiteCity.dat in your plugin directory with one you received from MaxMind.<hr>
<b>Accuracy of Free GeoIp database:</b> Over 99.3% on a country level and 76% on a city level for the US within a 25 mile radius.<br/>
<b>Accuracy of Paid GeoIp database:</b> Over 99.8% on a country level and 81% on a city level for the US within a 25 mile radius.</b>';

        $this->addImplementation('Core.loadSetting', 'GeoIp_Main', 'loadSetting');
    }

    public function onActivate() {
        //check if geo ip driver is active
        $geoipDriver = GeoIp_Driver::getInstance();
    }
}

?>
