<?php

class pap_update_4_3_123 {
	
	/**
	 * @var Gpf_File_Settings
	 */
	private $accountSettings;
	/**
	 * @var Gpf_File_Settings
	 */	
	private $globalSettings;
	
    public function execute() {
        $this->accountSettings = new Gpf_File_Settings(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
        $this->globalSettings = new Gpf_File_Settings();
        $this->globalSettings->getAll();
               
        $this->changeToGlobalSetting(Pap_Settings::PARAM_NAME_USER_ID);
        $this->changeToGlobalSetting(Pap_Settings::PARAM_NAME_BANNER_ID);
        $this->changeToGlobalSetting(Pap_Settings::PARAM_NAME_CAMPAIGN_ID);
        $this->changeToGlobalSetting(Pap_Settings::PARAM_NAME_ROTATOR_ID);
        $this->changeToGlobalSetting(Pap_Settings::PARAM_NAME_EXTRA_DATA);
        $this->changeToGlobalSetting(Pap_Settings::PARAM_NAME_EXTRA_DATA . '1');
        $this->changeToGlobalSetting(Pap_Settings::PARAM_NAME_EXTRA_DATA . '2');
        $this->changeToGlobalSetting(Pap_Settings::PARAM_NAME_DESTINATION_URL);
        $this->changeToGlobalSetting(Pap_Settings::DEBUG_TYPES);
        $this->changeToGlobalSetting(Pap_Settings::DELETE_COOKIE);
        $this->changeToGlobalSetting(Pap_Settings::P3P_POLICY_COMPACT);
        $this->changeToGlobalSetting(Pap_Settings::URL_TO_P3P);
        $this->changeToGlobalSetting(Pap_Settings::OVERWRITE_COOKIE);
        $this->changeToGlobalSetting(Pap_Settings::COOKIE_DOMAIN);
        $this->changeToGlobalSetting(Pap_Settings::IMPRESSIONS_TABLE_INPUT);
        $this->changeToGlobalSetting(Pap_Settings::IMPRESSIONS_TABLE_PROCESS);
        $this->changeToGlobalSetting(Pap_Settings::VISITS_TABLE_INPUT);
        $this->changeToGlobalSetting(Pap_Settings::VISITS_TABLE_PROCESS);
        $this->changeToGlobalSetting(Pap_Features_PayoutFieldsEncryption_Config::ENCRYPT_KEY);
        $this->changeToGlobalSetting(Pap_Features_PayoutFieldsEncryption_Config::ENCRYPT_IV);
    }
    
    private function changeToGlobalSetting($settingName) {
    	if ($this->accountSettings->hasSetting($settingName)) {
    		$this->globalSettings->setSetting($settingName, $this->accountSettings->getSetting($settingName)); 
    		$this->accountSettings->removeSetting($settingName); 		
    	}
    }
}
?>
