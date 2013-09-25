<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class CyberSource_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'CyberSource';
        $this->name = $this->_('CyberSource integration plugin');
        $this->description = $this->_('This plugin handles CyberSource Silent Order POST (integration of Post Affiliate with CyberSource).');
        $this->version = '1.0.0';
        $this->author = 'Juraj Simon';
        $this->addRequirement('PapCore', '4.2.0.18');
        $this->configurationClassName = 'CyberSource_Config';
        $this->addImplementation('Core.defineSettings', 'CyberSource_Main', 'initSettings');
    }
}
?>
