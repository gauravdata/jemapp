<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class BusinessCatalyst_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'BusinessCatalyst';
        $this->name = $this->_('Adobe Business Catalyst Plugin');
        $this->description = $this->_('This plugin handles Adobe Business Catalyst (integration of Post Affiliate with Adobe Business Catalyst)');
        $this->version = '1.0.0';
        $this->configurationClassName = 'BusinessCatalyst_Config';
        
        $this->addRequirement('PapCore', '4.4.0.0');
        
        $this->addImplementation('Core.defineSettings', 'BusinessCatalyst_Main', 'initSettings');
    }
}
?>
