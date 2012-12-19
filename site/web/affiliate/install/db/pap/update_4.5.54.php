<?php
class pap_update_4_5_54 {

    public function execute() {
        $settingsFile = new Gpf_File_Settings();
        if(!$settingsFile->hasSetting(Pap_Settings::P3P_POLICY_COMPACT)) {
            $settingsFile->setSetting(Pap_Settings::P3P_POLICY_COMPACT, Gpf_Settings::get(Pap_Settings::P3P_POLICY_COMPACT));
        }
        if(!$settingsFile->hasSetting(Pap_Settings::URL_TO_P3P)) {
            $settingsFile->setSetting(Pap_Settings::URL_TO_P3P, Gpf_Settings::get(Pap_Settings::URL_TO_P3P));
        }
    }
}
?>
