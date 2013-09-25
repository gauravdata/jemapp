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

class RefidLength_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'RefidLength';
        $this->name = $this->_('Referral ID length constraint');
        $this->description = $this->_('This plugin limits length of referral ID. Minimum and maximum length can be configured.');
        $this->version = '1.0.0';
        $this->configurationClassName = 'RefidLength_Config';
        
        $this->addRequirement('PapCore', '4.1.26.4');
        
        $this->addImplementation('Core.defineSettings', 'RefidLength_Main', 'initSettings');
        $this->addImplementation('Core.initPrivileges', 'RefidLength_Main', 'initPrivileges');
        $this->addImplementation('PostAffiliate.UsersTable.constraints', 'RefidLength_Main', 'process');
    }
}
?>
