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

/**
 * @package PostAffiliatePro plugins
 */
class NumberRefid_Main extends Gpf_Plugins_Handler {
    /**
     * @return RefidLength_Main
     */
	public static function getHandlerInstance() {
 		return new NumberRefid_Main();
 	}

 	public function addRefidConstraint(Pap_Db_Table_Users $context) {
 	    $context->addConstraint(new NumberRefid_AffiliateRegExpConstraint(Pap_Db_Table_Users::REFID,
                                    "/^[0-9]*$/",
                                    $this->_('Referral ID can contain only [0-9] characters. %s given')));
 	}

    public function generateRefid(Pap_Affiliates_User $user) {
        $user->setRefId($this->getNewNumberRefid());
    }

    public function generateRefidIntoForm(Gpf_Rpc_Form $form) {
        $form->setField('refid', $this->getNewNumberRefid());
    }

    private function getNewNumberRefid() {
        $codeGenerator = new Gpf_Common_CodeUtils_CodeGenerator('{99999999}');
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
