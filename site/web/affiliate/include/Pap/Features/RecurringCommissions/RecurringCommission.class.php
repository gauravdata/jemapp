<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: Commission.class.php 22311 2008-11-14 12:36:10Z mjancovic $
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
class Pap_Features_RecurringCommissions_RecurringCommission extends Pap_Db_RecurringCommission {

    /**
     * @return Gpf_Recurrence_Preset
     */
    public function getRecurrencePreset() {
        $recurrnecePreset = new Gpf_Recurrence_Preset();
        $recurrnecePreset->setId($this->getRecurrencePresetId());
        $recurrnecePreset->load();
        return $recurrnecePreset;
    }

    /**
     * @return Pap_Common_Transaction
     */
    public function getTransaction() {
        $transaction = new Pap_Common_Transaction();
        $transaction->setId($this->getTransactionId());
        $transaction->load();
        return $transaction;
    }

    public function createCommissions() {
        $comissionEntry = new Pap_Db_RecurringCommissionEntry();
        $comissionEntry->setRecurringCommissionId($this->getId());
        try {
            $parentTransaction = $this->getTransaction();
        } catch (Gpf_Exception $e) {
            $parentTransaction = null;
        }
        foreach ($comissionEntry->loadCollection() as $comissionEntry) {
            if (!$this->isExistsUser($comissionEntry->getUserId())) {
                Gpf_log::error('Recurring commissions - createCommissions: user does not exist: ' . $comissionEntry->getUserId());
                if ($comissionEntry->getTier() == '1') {
                    return;
                } else {
                    continue;
                }
            }
            $transaction = new Pap_Common_Transaction();
            $transaction->setDateInserted(Gpf_Common_DateUtils::now());
            $transaction->setType(Pap_Common_Constants::TYPE_RECURRING);
            $transaction->setTier($comissionEntry->getTier());
            $transaction->setUserId($comissionEntry->getUserId());
            $transaction->setCommissionTypeId($this->getCommissionTypeId());
            $transaction->setParentTransactionId($this->getTransactionId());
            $transaction->setCommission($comissionEntry->getCommission());
            $transaction->setPayoutStatus(Pap_Common_Transaction::PAYOUT_UNPAID);
            $transaction->setStatus(Pap_Common_Constants::STATUS_APPROVED);

            if ($parentTransaction != null) {
                $transaction->setOrderId($parentTransaction->getOrderId());
                $transaction->setProductId($parentTransaction->getProductId());
                $transaction->setTotalCost($parentTransaction->getTotalCost());
                $transaction->setCampaignId($parentTransaction->getCampaignId());
                $transaction->setBannerId($parentTransaction->getBannerId());
                $transaction->setParentBannerId($parentTransaction->getParentBannerId());
                $transaction->setCountryCode($parentTransaction->getCountryCode());
                $transaction->setData1($parentTransaction->getData1());
                $transaction->setData2($parentTransaction->getData2());
                $transaction->setData3($parentTransaction->getData3());
                $transaction->setData4($parentTransaction->getData4());
                $transaction->setData5($parentTransaction->getData5());
            }

            $transaction->save();
        }
    }

    private function isExistsUser($userId) {
        $user = new Pap_Common_User();
        $user->setId($userId);
        try {
            $user->load();
            return true;
        } catch (Gpf_DbEngine_NoRowException $e) {
            return false;
        }
    }
}
?>
