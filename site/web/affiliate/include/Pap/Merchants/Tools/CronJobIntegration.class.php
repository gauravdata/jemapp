<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
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
class Pap_Merchants_Tools_CronJobIntegration extends Gpf_Object {

    /**
     * @service cronjob read
     * 
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $runner = new Gpf_Tasks_Runner();
        if (!$runner->isRunningOK()) {
            $form->setField('warningMessage', $this->_('Cron job is not running'));
        }
        $form->setField('lastRunTime', $runner->getLastRunTime());
        $form->setField('cronCommand', $this->getCronCommand());
        $form->setField(Gpf_Settings_Gpf::CRON_RUN_INTERVAL, Gpf_Settings::get(Gpf_Settings_Gpf::CRON_RUN_INTERVAL));
        return $form;
    }
    
    /**
     * @service cronjob write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        Gpf_Settings::set(Gpf_Settings_Gpf::CRON_RUN_INTERVAL, $form->getFieldValue(Gpf_Settings_Gpf::CRON_RUN_INTERVAL));
        $form->setInfoMessage($this->_("Cron runtime saved"));
        return $form;
    }
    
    private function getCronCommand() {
        return '/usr/local/bin/php -q '.Gpf_Paths::getInstance()->getFullScriptsPath().'jobs.php';
    }
}

?>
