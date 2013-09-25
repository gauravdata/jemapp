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

class UsernameConstraint_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'UsernameConstraint';
        $this->name = $this->_('Username constraint');
        $this->description = $this->_('Enables custom username. You will be able to set regular expression pattern in configure screen after plugin activation.');
        $this->version = '1.0.0';
        $this->configurationClassName = 'UsernameConstraint_Config';        
        $this->addRequirement('PapCore', '4.2.10.4');

        $this->addImplementation('Core.defineSettings', 'UsernameConstraint_Main', 'initSettings');
        $this->addImplementation('AuthUsers.initConstraints', 'UsernameConstraint_Main', 'addUsernameConstraint');
    }
    
    public function onActivate() {
        if (Gpf_Settings::get(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES)==Gpf::NO) {
            throw new Gpf_Exception($this->_('Setting "do not force email usernames" must be active!'));
        }
    }
}
?>
