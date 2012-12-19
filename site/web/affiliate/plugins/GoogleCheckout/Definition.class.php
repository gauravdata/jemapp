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

class GoogleCheckout_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'GoogleCheckout';
        $this->name = $this->_('GoogleCheckout notifications handling');
        $this->description = $this->_('This plugin handles GoogleCheckout notifications (integration of Post Affiliate with GoogleCheckout)');
        $this->version = '1.0.0';
        $this->configurationClassName = 'GoogleCheckout_Config';
        
        $this->addRequirement('PapCore', '4.2.3.2');
        
        $this->addImplementation('Core.defineSettings', 'GoogleCheckout_Main', 'initSettings');
    }
}
?>
