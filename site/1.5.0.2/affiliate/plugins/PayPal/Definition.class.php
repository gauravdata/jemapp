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

class PayPal_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'PayPal';
        $this->name = $this->_('PayPal IPN handling');
        $this->description = $this->_('This plugin handles PayPal IPN notifications (integration of Post Affiliate with PayPal)');
        $this->version = '1.0.1';
        $this->configurationClassName = 'PayPal_Config';
        
        $this->addRequirement('PapCore', '4.0.4.6');
        
        $this->addImplementation('Core.defineSettings', 'Paypal_Main', 'initSettings');
    }
}
?>
