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

class AuthorizeNet_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'Authorize.net';
        $this->name = $this->_('Authorize.net Silent Order handling');
        $this->description = $this->_('This plugin handles Authorize.net Silent Order notifications (integration of Post Affiliate with Authorize.net)');
        $this->version = '1.0.0';
        $this->configurationClassName = 'AuthorizeNet_Config';
        
        $this->addRequirement('PapCore', '4.3.125.0');
        $this->addImplementation('Core.defineSettings', 'AuthorizeNet_Main', 'initSettings');
        
    }
}
?>
