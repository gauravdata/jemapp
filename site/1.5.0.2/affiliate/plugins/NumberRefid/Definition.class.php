<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Peter Veres
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class NumberRefid_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'NumberRefid';
        $this->name = $this->_('Only numbers in Referral ID');
        $this->description = $this->_('Only numbers in Referral ID.');
        $this->version = '1.0.0';

        $this->addRequirement('PapCore', '4.0.4.6');

        $this->addImplementation('PostAffiliate.UsersTable.constraints', 'NumberRefid_Main', 'addRefidConstraint');
        $this->addImplementation('PostAffiliate.AffiliateForm.setDefaultDbRowObjectValues', 'NumberRefid_Main', 'generateRefid');
        $this->addImplementation('PostAffiliate.AffiliateForm.checkRefidIsValid', 'NumberRefid_Main', 'generateRefidIntoForm');
    }
}
?>
