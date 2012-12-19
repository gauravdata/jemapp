<?php
/**
 * Update step will delete plugin configuration file in every account 
 *
 */
class pap_update_4_1_7 {
    public function execute() {
        $createAccountTask = new Gpf_Install_CreateAccountTask();
        $createAccountTask->addRecurrencePreset('varied', Gpf_Lang::_runtime("Varied"));
    }
}
?>
