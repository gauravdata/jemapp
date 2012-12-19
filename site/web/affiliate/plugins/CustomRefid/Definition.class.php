<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class CustomRefid_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'CustomRefid';
        $this->name = $this->_('Custom refid');
        $this->description = $this->_('Enables custom refid. You will be able to set refid pattern in configure screen after plugin activation.');
        $this->version = '1.0.0';
        $this->configurationClassName = 'CustomRefid_Config';        
        $this->addRequirement('PapCore', '4.2.5.0');

        $this->addImplementation('Core.defineSettings', 'CustomRefid_Main', 'initSettings');
        $this->addImplementation('PostAffiliate.UsersTable.constraints', 'CustomRefid_Main', 'addRefidConstraint');
        $this->addImplementation('PostAffiliate.AffiliateForm.setDefaultDbRowObjectValues', 'CustomRefid_Main', 'generateRefid');
        $this->addImplementation('PostAffiliate.AffiliateForm.checkRefidIsValid', 'CustomRefid_Main', 'generateRefidIntoForm');
    }
}
?>
