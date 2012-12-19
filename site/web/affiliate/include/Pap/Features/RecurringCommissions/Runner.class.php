<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
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
class Pap_Features_RecurringCommissions_Runner extends Gpf_Tasks_LongTask {

    public function getName() {
        return $this->_('Recurring commissions');
    }
    protected function execute() {

        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_RecurringCommissions::getInstance());
        $select->from->add(Pap_Db_Table_RecurringCommissions::getName());

        foreach ($select->getAllRowsIterator() as $row) {
            $recurringCommission = new Pap_Features_RecurringCommissions_RecurringCommission();
            $recurringCommission->fillFromRecord($row);
            $recurringCommission->setPersistent(true);

            if ($this->isDone($recurringCommission->getId())) {
                continue;
            }

            $recurrencePreset = $recurringCommission->getRecurrencePreset();
            $lastCommissionDate = $recurringCommission->getLastCommissionDate();
            if ($lastCommissionDate == null) {
                $lastCommissionDate = $recurringCommission->getTransaction()->getDateInserted();
            }
            $nextTimestamp = $recurrencePreset->getNextDate(Gpf_Common_DateUtils::mysqlDateTime2Timestamp($lastCommissionDate));
            if ($nextTimestamp == null || $nextTimestamp > time()) {
                continue;
            }
            $recurringCommission->setLastCommissionDate(Gpf_Common_DateUtils::getDateTime($nextTimestamp));
            if ($recurringCommission->getStatus() == Pap_Common_Constants::STATUS_APPROVED) {
                try {
                    $recurringCommission->createCommissions();
                } catch (Gpf_Exception $e) {
                    Gpf_Log::critical('Recurring commissions - error create commissions: ' . $e->getMessage());
                    $this->setDone();
                }
            }
            $recurringCommission->save();

            $this->setDone();
        }
    }

}
?>
