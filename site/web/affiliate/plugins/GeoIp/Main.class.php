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
class GeoIp_Main extends Gpf_Plugins_Handler {

    /**
     * @return GoogleMaps_Main
     */
    public static function getHandlerInstance() {
        return new GeoIp_Main();
    }

    private function hasActiveDriver() {
        try {
            $geoipDriver = GeoIp_Driver::getInstance();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function loadSetting(Gpf_ApplicationSettings $context) {
        if ($this->hasActiveDriver()) {
            $context->addValue('geoip', 'Y');
        }
    }

    public function initUserMailTemplateVariables(Gpf_Mail_Template $template) {
        /*
         * this method was removed in rev. 26588, it was added back only for cause of update
         * if user has active GeoIp plugin and try to update to higher version , installer thorws an error
         * Fatal error:  Call to undefined method GeoIp_Main::initUserMailTemplateVariables()
         */
    }
}
?>
