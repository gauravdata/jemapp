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

class MandatoryRefid_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'MandatoryRefid';
        $this->name = $this->_('Mandatory referral id for affiliate');
        $this->description = $this->_('This plugin makes referral id mandatory in signup form and disables editing of referral id by affiliate.');
        $this->version = '1.0.0';
        
        $this->addRequirement('PapCore', '4.0.4.6');
        
        $this->addImplementation('PostAffiliate.UsersTable.constraints', 'MandatoryRefid_Main', 'addRefidConstraint');
    }
}
?>
