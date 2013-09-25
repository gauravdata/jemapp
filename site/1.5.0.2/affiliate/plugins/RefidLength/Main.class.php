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
class RefidLength_Main extends Gpf_Plugins_Handler {
    const SETTING_REFID_MIN_LENGTH = 'minimumRefidLength';
    const SETTING_REFID_MAX_LENGTH = 'maximumRefidLength';

    public function initSettings($context) {
        $context->addDbSetting(self::SETTING_REFID_MIN_LENGTH, 0);
        $context->addDbSetting(self::SETTING_REFID_MAX_LENGTH, 20);
    }

    /**
     * @return RefidLength_Main
     */
	public static function getHandlerInstance() {
 		return new RefidLength_Main();
 	}

 	public function process(Pap_Db_Table_Users $context) {
 	    $context->addConstraint(new Gpf_DbEngine_Row_RefIdLengthConstraint(
                                            Pap_Db_Table_Users::REFID,
                                            Gpf_Settings::get(self::SETTING_REFID_MIN_LENGTH),
                                            Gpf_Settings::get(self::SETTING_REFID_MAX_LENGTH),
                                            $this->_('Referral id must be longer than %s characters'),
                                            $this->_('Referral id can not be longer than %s characters')));
		return Gpf_Plugins_Engine::PROCESS_CONTINUE;
 	}

    public function initPrivileges(Gpf_Privileges $privileges) {
        $privileges->addPrivilege('refidlength', Gpf_Privileges::P_ALL);
    }
}
?>
