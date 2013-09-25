<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
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

class SignupToPrivateCampaigns_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'SignupToPrivateCampaigns';
        $this->name = $this->_('Signup affiliate automatically to private campaigns');
        $this->description = $this->_('This plugin signs up affiliate to private campaigns defined by you after signup. Campaigns IDs must be set in plugin\'s configuration.');
        $this->version = '1.0.0';
        
        $this->addRequirement('PapCore', '4.4.0.0');
        $this->configurationClassName = 'SignupToPrivateCampaigns_Config';
        
        $this->addImplementation('Core.defineSettings', 'SignupToPrivateCampaigns_Main', 'initSettings');        
        $this->addImplementation('PostAffiliate.signup.after', 'SignupToPrivateCampaigns_Main', 'afterSignup');
        $this->addImplementation('PostAffiliate.affiliate.firsttimeApproved', 'SignupToPrivateCampaigns_Main', 'firstTimeApproved');
    }
}
?>
