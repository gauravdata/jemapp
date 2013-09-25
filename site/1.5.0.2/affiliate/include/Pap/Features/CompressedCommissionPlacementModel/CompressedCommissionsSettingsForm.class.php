<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Matej Kendera
 *   @since Version 1.0.0
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
class Pap_Features_CompressedCommissionPlacementModel_CompressedCommissionsSettingsForm extends Gpf_Object {

    /**
     *
     * @service compressed_commission_placement_model read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(Pap_Features_CompressedCommissionPlacementModel_Definition::ACTION, Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::ACTION));
        $form->addField(Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE, Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE));
        $form->addField(Pap_Features_CompressedCommissionPlacementModel_Definition::PROCESSING_ENABLED, Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::PROCESSING_ENABLED));
        $form->addField(Pap_Db_Table_Rules::EQUATION, Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION));
        $form->addField(Pap_Db_Table_Rules::EQUATION_VALUE_1, Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION_VALUE1));
        $form->addField(Pap_Db_Table_Rules::EQUATION_VALUE_2, Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION_VALUE2));
        $form->addField(Pap_Db_Table_Rules::STATUS, Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_STATUS));
        $form->addField(Pap_Db_Table_Rules::WHAT, Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_WHAT));
        return $form;
    }

    /**
     *
     * @service compressed_commission_placement_model write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(Pap_Features_CompressedCommissionPlacementModel_Definition::ACTION, $form->getFieldValue(Pap_Features_CompressedCommissionPlacementModel_Definition::ACTION));
        Gpf_Settings::set(Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE, $form->getFieldValue(Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE));
        Gpf_Settings::set(Pap_Features_CompressedCommissionPlacementModel_Definition::PROCESSING_ENABLED, $form->getFieldValue(Pap_Features_CompressedCommissionPlacementModel_Definition::PROCESSING_ENABLED));
        Gpf_Settings::set(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION, $form->getFieldValue(Pap_Db_Table_Rules::EQUATION));
        Gpf_Settings::set(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION_VALUE1, $form->getFieldValue(Pap_Db_Table_Rules::EQUATION_VALUE_1));
        Gpf_Settings::set(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_EQUATION_VALUE2, $form->getFieldValue(Pap_Db_Table_Rules::EQUATION_VALUE_2));
        Gpf_Settings::set(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_STATUS, $form->getFieldValue(Pap_Db_Table_Rules::STATUS));
        Gpf_Settings::set(Pap_Features_CompressedCommissionPlacementModel_Definition::RULE_WHAT, $form->getFieldValue(Pap_Db_Table_Rules::WHAT));

        $this->updateCompressedCommissionsTask($this->getRecurrencePreset($form->getFieldValue(Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE)));

        $form->setInfoMessage($this->_('Compressed commission rule saved'));
        return $form;
    }

    private function updateCompressedCommissionsTask($recurrencePreset) {
        if (Gpf_Settings::get(Pap_Features_CompressedCommissionPlacementModel_Definition::PROCESSING_ENABLED) == Gpf::YES) {
            $this->insertCompressedCommissionsTask($recurrencePreset);
        } else {
            $this->removeCompressedCommissionsTask();
        }
    }

    private function insertCompressedCommissionsTask($recurrencePreset) {
        $plannedTask = $this->createCompressedCommissionTask();
        try {
            $plannedTask->loadFromData();
            $plannedTask->setRecurrencePresetId($recurrencePreset);
            $plannedTask->save();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $plannedTask->setRecurrencePresetId($recurrencePreset);
            $plannedTask->insert();
        }
    }

    private function removeCompressedCommissionsTask() {
        try {
            $plannedTask = $this->createCompressedCommissionTask();
            $plannedTask->loadFromData();
            $plannedTask->delete();
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
    }

    private function createCompressedCommissionTask() {
        $plannedTask = new Gpf_Db_PlannedTask();
        $plannedTask->setClassName('Pap_Features_CompressedCommissionPlacementModel_Task');
        return $plannedTask;
    }

    private function getRecurrencePreset($recurrence) {
        switch ($recurrence) {
            case Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE_WEEKLY:
                return Pap_Db_CommissionType::RECURRENCE_WEEKLY;
            case Pap_Features_CompressedCommissionPlacementModel_Definition::RECURRENCE_MONTHLY:
                return Pap_Db_CommissionType::RECURRENCE_MONTHLY;
        }
    }

}

?>
