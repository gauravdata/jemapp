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
class GoogleMaps_Config extends Gpf_Plugins_Config {
    const FIELD_KEY = 'key';
    
    protected function initFields() {
        $this->addTextBox("Google Maps API Key", self::FIELD_KEY, "Go to http://code.google.com/apis/maps/signup.html");        
    }
    
    /**
     * Save google maps api key
     *
     * @anonym
     * @service googlemaps write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(GoogleMaps_Main::SETTING_MAP_API_KEY, $form->getFieldValue(self::FIELD_KEY));
        $form->setInfoMessage($this->_('Google maps key saved'));
        return $form;
    }

    /**
     * Load google maps api key
     *
     * @anonym
     * @service googlemaps read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(self::FIELD_KEY, Gpf_Settings::get(GoogleMaps_Main::SETTING_MAP_API_KEY));
        return $form;
    }
}

?>
