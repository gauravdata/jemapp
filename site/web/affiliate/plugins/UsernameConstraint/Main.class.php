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

/**
 * @package PostAffiliate
 */
class UsernameConstraint_Main extends Gpf_Plugins_Handler {

	public static function getHandlerInstance() {
		return new UsernameConstraint_Main();
	}

	public function initSettings($context) {
		$context->addDbSetting(UsernameConstraint_Config::CUSTOM_USERNAME_FORMAT, '/.*/');
		$context->addDbSetting(UsernameConstraint_Config::CUSTOM_ERROR_MESSAGE, 'Wrong username format!');
	}

	public function addUsernameConstraint(Gpf_Db_Table_AuthUsers $usersTable) {
		$usersTable->addConstraint(new Gpf_DbEngine_Row_RegExpConstraint(Gpf_Db_Table_AuthUsers::USERNAME, Gpf_Settings::get(UsernameConstraint_Config::CUSTOM_USERNAME_FORMAT), Gpf_Settings::get(UsernameConstraint_Config::CUSTOM_ERROR_MESSAGE)));
	}
}
?>
