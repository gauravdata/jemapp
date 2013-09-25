<?php
/**
 * Update step will delete plugin configuration file in every account 
 *
 */
class pap_update_4_0_9 {
    public function execute() {
        $createAccountTask = new Pap_Install_CreateAccountTask();
        $createAccountTask->setupDefaultRecurrencePresets();
    }
}
?>
