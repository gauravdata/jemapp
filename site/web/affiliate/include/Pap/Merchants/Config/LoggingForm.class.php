<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: LoggingForm.class.php 29020 2010-08-06 13:48:27Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Config_LoggingForm extends Pap_Merchants_Config_TaskSettingsFormBase {
    const DEFAULT_LOG_LEVEL = Gpf_Log::INFO;

    /**
     * @service logging_setting read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Gpf_Settings_Gpf::LOG_LEVEL_SETTING_NAME, Gpf_Settings::get(Gpf_Settings_Gpf::LOG_LEVEL_SETTING_NAME));
       	$form->setField(Pap_Settings::DEBUG_TYPES, Gpf_Settings::get(Pap_Settings::DEBUG_TYPES));
        $form->setField(Pap_Settings::AUTO_DELETE_EVENTS, Gpf_Settings::get(Pap_Settings::AUTO_DELETE_EVENTS));
        $form->setField(Pap_Settings::AUTO_DELETE_EVENTS_RECORDS_NUM, Gpf_Settings::get(Pap_Settings::AUTO_DELETE_EVENTS_RECORDS_NUM));

        $form->setField(Pap_Settings::AUTO_DELETE_LOGINSHISTORY, Gpf_Settings::get(Pap_Settings::AUTO_DELETE_LOGINSHISTORY));

       	return $form;
    }

    protected function isCronRunning() {
        $taskRunner = new Gpf_Tasks_Runner();
        return $taskRunner->isRunningOK();
    }

    protected function saveSettings($form) {
        Gpf_Settings::set(Gpf_Settings_Gpf::LOG_LEVEL_SETTING_NAME, $form->getFieldValue(Gpf_Settings_Gpf::LOG_LEVEL_SETTING_NAME));
        Gpf_Settings::set(Pap_Settings::DEBUG_TYPES, $form->getFieldValue(Pap_Settings::DEBUG_TYPES));
        Gpf_Settings::set(Gpf_Settings_Gpf::AUTO_DELETE_EVENTS, $form->getFieldValue(Gpf_Settings_Gpf::AUTO_DELETE_EVENTS));
        Gpf_Settings::set(Gpf_Settings_Gpf::AUTO_DELETE_EVENTS_RECORDS_NUM, $form->getFieldValue(Gpf_Settings_Gpf::AUTO_DELETE_EVENTS_RECORDS_NUM));
        Gpf_Settings::set(Gpf_Settings_Gpf::AUTO_DELETE_LOGINSHISTORY, $form->getFieldValue(Gpf_Settings_Gpf::AUTO_DELETE_LOGINSHISTORY));
    }

    /**
     * @service logging_setting write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        if ($this->isCronRunning() === false) {
            $form->setErrorMessage($this->_("It seems so, that your cron is not running. Please setup your cron properly."));
        }

        $this->saveSettings($form);
        $this->insertDeleteSettingsTask();

        $form->setInfoMessage($this->_("Logging saved"));
        return $form;
    }

    public function insertDeleteSettingsTask() {
        if (Gpf_Settings::get(Gpf_Settings_Gpf::AUTO_DELETE_EVENTS) > 0 || Gpf_Settings::get(Gpf_Settings_Gpf::AUTO_DELETE_EVENTS_RECORDS_NUM) > 0) {
            $this->insertTask('Gpf_Log_DeleteEventsTask');
        } else {
            $this->removeTask('Gpf_Log_DeleteEventsTask');
        }
        if (Gpf_Settings::get(Gpf_Settings_Gpf::AUTO_DELETE_LOGINSHISTORY) > 0) {
            $this->insertTask('Gpf_Log_DeleteLoginsHistoryTask');
        } else {
            $this->removeTask('Gpf_Log_DeleteLoginsHistoryTask');
        }
    }
}

?>
