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

class SignupActionCommissions_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'SignupActionCommissions';
        $this->name = $this->_('Action commission for signup');
        $this->description = $this->_('This plugin helps you to use action commission for signups. You need to have action commission feature activated! Create an action commission in a campaign and remember its code. After activation of this plugin, configure it and set the action commission code. You are also allowed to define campaign ID to apply action from campaign that is not set as default.');
        $this->version = '1.1.0';
        $this->configurationClassName = 'SignupActionCommissions_Config';
        
        $this->addRequirement('PapCore', '4.1.10.1');
        
        $this->addImplementation('Core.defineSettings', 'SignupActionCommissions_Main', 'initSettings');
        $this->addImplementation('PostAffiliate.signup.after', 'SignupActionCommissions_Main', 'addCommission');
    }
}
?>
