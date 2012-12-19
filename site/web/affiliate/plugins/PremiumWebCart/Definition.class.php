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

class PremiumWebCart_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'PremiumWebCart';
        $this->name = $this->_('PremiumWebCart');
        $this->description = $this->_('This plugin handles PremiumWebCart integration with PAP');
        $this->version = '1.0.0';
        $this->configurationClassName = 'PremiumWebCart_Config';
        
        $this->addRequirement('PapCore', '4.2.41.2');
        
        $this->addImplementation('Core.defineSettings', 'PremiumWebCart_Main', 'initSettings');
    }
}
?>
