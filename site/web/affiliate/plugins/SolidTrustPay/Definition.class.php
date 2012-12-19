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

class SolidTrustPay_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = '';
        $this->name = $this->_('SolidTrustPay IPN handling');
        $this->description = $this->_('This plugin handles SolidTrustPay IPN (integration of Post Affiliate with SolidTrustPay)');
        $this->version = '1.0.1';
        $this->configurationClassName = 'SolidTrustPay_Config';
        
        $this->addRequirement('PapCore', '4.2.3.2');
        
        $this->addImplementation('Core.defineSettings', 'SolidTrustPay_Main', 'initSettings');
    }
}
?>
