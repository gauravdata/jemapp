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

class TwoCheckout_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = '2Checkout';
        $this->name = $this->_('2Checkout');
        $this->description = $this->_('This plugin handles 2Checkout integration with PAP');
        $this->version = '1.0.0';
        $this->configurationClassName = 'TwoCheckout_Config';
        
        $this->addRequirement('PapCore', '4.5.52.8');
        
        $this->addImplementation('Core.defineSettings', 'TwoCheckout_Main', 'initSettings');
    }
}
?>
