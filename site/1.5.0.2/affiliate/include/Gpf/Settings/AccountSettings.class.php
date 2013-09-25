<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric, Andrej Harsani, Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Settings.class.php 18002 2008-05-13 18:31:39Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Settings_AccountSettings extends Gpf_Object {

	/**
	 * @var Gpf_Settings_Base
	 */
	private $accountSettings;
	/**
	 * @var Gpf_Settings_Base
	 */
	private $settings;	

	public function __construct(Gpf_Settings_Base $accountSettings, Gpf_Settings_Base $settings) {
		$this->accountSettings = $accountSettings;
		$this->settings = $settings;
	}

	public function set($name, $value) {
		if ($this->isAccountSetting($name)) {
			$this->accountSettings->writeSetting($name, $value);
			return;
		} 
		$this->settings->writeSetting($name, $value);
	}

	public function get($name) {
		if ($this->isAccountSetting($name)) {
			return $this->accountSettings->readSetting($name);
		}
		return $this->settings->readSetting($name);
	}
	
	private function isAccountSetting($name) {
		return $this->accountSettings->getSettingsDefine()->isAccountSetting($name);
	}
}
?>
