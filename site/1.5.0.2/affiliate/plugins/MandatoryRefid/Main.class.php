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

/**
 * @package PostAffiliatePro plugins
 */
class MandatoryRefid_Main extends Gpf_Plugins_Handler {

	public static function getHandlerInstance() {
 		return new MandatoryRefid_Main();
 	}

 	public function addRefidConstraint(Pap_Db_Table_Users $usersTable) {
        $usersTable->addConstraint(new MandatoryRefid_Constraint());
		return Gpf_Plugins_Engine::PROCESS_CONTINUE;
 	}
}

class MandatoryRefid_Constraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {
    public function __construct() {
    }

    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
       return $this->validateUser($row);
    }

    private function validateUser(Pap_Db_User $user) {
        if ($user->getType() != Pap_Application::ROLETYPE_AFFILIATE) {
            return;
        }
        if ($user->getRefId() == "") {
            throw new Gpf_DbEngine_Row_ConstraintException(Pap_Db_Table_Users::REFID, $this->_("Referral ID can not be blank"));
        }
        if (Gpf_Session::getAuthUser()->isLogged() && Gpf_Session::getAuthUser()->isAffiliate()) {
            $userTmp = new Pap_Db_User();
            $userTmp->setId($user->getId());
            $userTmp->load();
            if ($userTmp->getRefId() != $user->getRefId()) {
                throw new Gpf_DbEngine_Row_ConstraintException(Pap_Db_Table_Users::REFID, $this->_("Referral ID can not be changed"));
            }
        }
    }
}

?>
