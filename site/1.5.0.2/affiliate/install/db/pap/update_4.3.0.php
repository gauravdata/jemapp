<?php
class pap_update_4_3_0 {
    public function execute() {
        $papSettings = new Pap_Settings();
        $papSettings->writeDefaultFileSettings();
    }
}
?>
