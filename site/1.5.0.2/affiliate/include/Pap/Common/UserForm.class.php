<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CustomerForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
abstract class Pap_Common_UserForm extends Gpf_View_FormService {
    
	/**
     * @var Pap_Common_User
     */
    protected $user;
    
    protected abstract function getDefaultUserRole();
    
    protected function checkUsernameIsValidEmail(Gpf_Rpc_Form $form, $operationType) {
        $username = $form->getFieldValue("username");
        $emailValidator = new Gpf_Rpc_Form_Validator_EmailValidator();

        if(Gpf::YES == Gpf_Settings::get(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES) || $emailValidator->validate($username)) {
            return true;
        }

        $form->setFieldError("username", $this->_("Username must be valid email address"));
        return false;
    }
}

?>
