<?php
class pap_update_4_1_36 {

	public function execute() {
        $accountTask = new Pap_Install_CreateAccountTask();
        $accountTask->saveDefaultParamNameSettings();
	}
}
?>
