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

class UsernameRefid_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'UsernameRefid';
        $this->name = $this->_('Username Referral IDs');
        $this->description = $this->_('Username will be used as Referral IDs');
        $this->version = '1.0.0';
        
        $this->addRequirement('PapCore', '4.1.14.0');
        
        $this->addImplementation('PostAffiliate.User.beforeSave', 'UsernameRefid_Main', 'setUsernameRefid');
    }
    
    public function onActivate() {
    	Gpf_Settings::set(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES, Gpf::YES);
    }
}
?>
