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
class GoogleMaps_Main extends Gpf_Plugins_Handler {
    const SETTING_MAP_API_KEY = 'MAP_API_KEY';

    /**
     * @return GoogleMaps_Main
     */
    public static function getHandlerInstance() {
        return new GoogleMaps_Main();
    }

    /**
     * Load javascript library required by google maps
     *
     * @param Gpf_Contexts_Module $context
     */
    public function initJsResources(Gpf_Contexts_Module $context) {
        if (($key = $this->getKey()) !== false) {
            $context->addJsResource(
 	          'http://maps.google.com/maps?gwt=1&amp;file=api&amp;v=2.x&amp;key=' . $key);
            $context->addJsResource(
              'https://www.google.com/jsapi?key=' . $key);
        }
    }

    public function initSettings($context) {
        $context->addDbSetting(self::SETTING_MAP_API_KEY, '');
    }

    private function getKey() {
        $key = '';
        try {
            $key = Gpf_Settings::get(self::SETTING_MAP_API_KEY);
        } catch (Exception $e) {
        }

        if (strlen($key) || $_SERVER['HTTP_HOST'] == 'localhost') {
            return $key;
        }
        return false;
    }

    public function loadSetting(Gpf_ApplicationSettings $context) {
        if ($this->getKey() !== false) {
            $context->addValue('googlemaps', 'Y');
        }
    }

    public function initPrivileges(Gpf_Privileges $privileges) {
        $privileges->addPrivilege('googlemaps', Gpf_Privileges::P_ALL);
    }
}
?>
