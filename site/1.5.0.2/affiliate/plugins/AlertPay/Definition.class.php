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

class AlertPay_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'AlertPay';
        $this->name = $this->_('AlertPay integration plugin');
        $this->description = $this->_('This plugin handles AlertPay IPN notifications (integration of Post Affiliate with AlertPay). Do not forget to configure this plugin after activation.');
        $this->version = '1.0.0';
        $this->configurationClassName = 'AlertPay_Config';
        
        $this->addRequirement('PapCore', '4.0.4.6');
        
        $this->addImplementation('Core.defineSettings', 'AlertPay_Main', 'initSettings');
    }
}
?>
