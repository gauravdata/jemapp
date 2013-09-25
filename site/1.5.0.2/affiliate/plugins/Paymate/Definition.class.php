<?php

/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
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

class Paymate_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'Paymate';
        $this->name = $this->_('Paymate Express');
        $this->description = $this->_('This plugin handles Paymate Express buttons');
        $this->version = '1.0.0';
        $this->configurationClassName = 'Paymate_Config';
        
        $this->addRequirement('PapCore', '4.2.3.2');
        
        $this->addImplementation('Core.defineSettings', 'Paymate_Main', 'initSettings');
    }
}
?>
