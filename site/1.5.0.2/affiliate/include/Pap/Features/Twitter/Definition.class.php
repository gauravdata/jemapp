<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_Twitter_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'Twitter';
        $this->name = $this->_('Twitter Connect');
        $this->description = $this->_('Help your affiliates to tweet about your banners through Twitter.');
        $this->version = '1.0.0';
        $this->pluginType = self::PLUGIN_TYPE_FEATURE;
    }
}
?>
