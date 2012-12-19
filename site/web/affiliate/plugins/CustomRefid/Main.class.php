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

/**
 * @package PostAffiliate
 */
class CustomRefid_Main extends Gpf_Plugins_Handler {

	public static function getHandlerInstance() {
		return new CustomRefid_Main();
	}

	public function initSettings($context) {
		$context->addDbSetting(CustomRefid_Config::CUSTOM_REFID_FORMAT, '{XXXXXXXXXXXXX}');
	}

	public function addRefidConstraint(Pap_Db_Table_Users $usersTable) {
		$usersTable->addConstraint(new CustomRefid_CustomRefidConstraint());
	}

	public function generateRefid(Pap_Affiliates_User $user) {
        $user->setRefId($this->getNewRefid());
    }

    public function generateRefidIntoForm(Gpf_Rpc_Form $form) {
        $form->setField('refid', $this->getNewRefid());
    }

    private function getNewRefid() {
        $codeGenerator = new Gpf_Common_CodeUtils_CodeGenerator(Gpf_Settings::get(CustomRefid_Config::CUSTOM_REFID_FORMAT));
        for ($i = 1; $i <= 5; $i++) {
            $refid = $codeGenerator->generate();
            try {
                Pap_Affiliates_User::loadFromId($refid);
            } catch (Gpf_Exception $e) {
                return $refid;
            }
        }
	}
}
?>
