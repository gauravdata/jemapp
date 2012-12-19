<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class SignupToCampaign_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'SignupToCampaign';
        $this->name = $this->_('Signup affiliate automatically to campaign');
        $this->description = $this->_('This plugin signs up affiliate to campaign after signup. Campaign ID must be passed in a_cid parameter.');
        $this->version = '1.0.0';
        
        $this->addRequirement('PapCore', '4.2.0.10');
        
        $this->addImplementation('PostAffiliate.signup.after', 'SignupToCampaign_Main', 'afterSignup');
        $this->addImplementation('PostAffiliate.signup.afterFail', 'SignupToCampaign_Main', 'afterSignupFailed');
    }
}
?>
