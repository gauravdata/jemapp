<?php

class pap_update_4_3_130 {
    public function execute() {
        $loggingForm = new Pap_Merchants_Config_LoggingForm();
        $loggingForm->insertDeleteSettingsTask();
    }
}
?>
