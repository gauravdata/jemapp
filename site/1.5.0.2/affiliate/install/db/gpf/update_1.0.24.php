<?php

class gpf_update_1_0_24 {

	public function execute() {
		$accountSettings = new Gpf_File_Settings(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
		$globalSettings = new Gpf_File_Settings();
		$globalSettings->getAll();

		$settingName = Gpf_Settings_Gpf::BENCHMARK_ACTIVE;
		if ($accountSettings->hasSetting($settingName)) {
			$globalSettings->setSetting($settingName, $accountSettings->getSetting($settingName));
			$accountSettings->removeSetting($settingName);
		}		
	}
}
?>
