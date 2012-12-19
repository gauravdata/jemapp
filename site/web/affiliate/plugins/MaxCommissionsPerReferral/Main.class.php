<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
 * @package PostAffiliate
 */
class MaxCommissionsPerReferral_Main extends Gpf_Plugins_Handler {

    const MAX_COMMISSIONS_PER_REFERRAL_NUMBER = 'MaxCommissions';
    const MAX_COMMISSIONS_PER_REFERRAL_PERIOD = 'MaxCommissionsPeriod';

    private static $instance = false;
    /**
     * @return MaxCommissionsPerReferral_Main
     */

    private function __construct() {
    }

    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new MaxCommissionsPerReferral_Main();
        }
        return self::$instance;
    }

    public function initFields(Pap_Merchants_Campaign_CommissionTypeEditAdditionalForm $additionalDetails) {
        $additionalDetails->addTextBoxWithDefault($this->_('Maximum commissions per referral'), 
            self::MAX_COMMISSIONS_PER_REFERRAL_NUMBER, 
            "-1", $this->_("unlimited"),
            $this->_('Maximum commission per one customer'));
        $additionalDetails->addTextBoxWithDefault($this->_('Maximum commissions time period in seconds'),
            self::MAX_COMMISSIONS_PER_REFERRAL_PERIOD,
            "-1", $this->_("all time"),
            $this->_('Time Period in seconds in which are commission per one customer counted.'));
    }

    public function save(Gpf_Rpc_Form $form) {
        $maxCommPerReferralNumber = $form->getFieldValue(self::MAX_COMMISSIONS_PER_REFERRAL_NUMBER);
        $maxCommPerReferralPeriod = $form->getFieldValue(self::MAX_COMMISSIONS_PER_REFERRAL_PERIOD);
        
        if (is_numeric($maxCommPerReferralNumber) != "integer") {
            $form->setErrorMessage($this->_('Wrong format used for Maximum commissions per referral.'));
            return;    
        }
        
        if (is_numeric($maxCommPerReferralPeriod) != "integer") {
            $form->setErrorMessage($this->_('Wrong format used for Maximum commissions time period in seconds.'));
            return;
        }
        
        $commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
        $commTypeAttr->setCommissionTypeAttributeValue($form->getFieldValue('Id'), self::MAX_COMMISSIONS_PER_REFERRAL_NUMBER,
            $maxCommPerReferralNumber);
        $commTypeAttr->setCommissionTypeAttributeValue($form->getFieldValue('Id'), self::MAX_COMMISSIONS_PER_REFERRAL_PERIOD,
            $maxCommPerReferralPeriod);
    }

    public function load(Gpf_Rpc_Form $form) {
        $form->setField(self::MAX_COMMISSIONS_PER_REFERRAL_NUMBER,
    	$this->getCommissionsAttributeWithDefaultValue($form->getFieldValue('Id'), self::MAX_COMMISSIONS_PER_REFERRAL_NUMBER, -1));
        
    	$form->setField(self::MAX_COMMISSIONS_PER_REFERRAL_PERIOD,
    	$this->getCommissionsAttributeWithDefaultValue($form->getFieldValue('Id'), self::MAX_COMMISSIONS_PER_REFERRAL_PERIOD, -1));
    }

    /**
     *
     * @param $context
     */
    public function setSaveCommission(Pap_Contexts_Action $context) {
        $context->debug('MaxCommission per referral checking.');
        $commissionTypeId = $this->getCommissionTypeId($context);
        if ($commissionTypeId == null) {
            $context->debug('MaxCommission Commission type is null. Ended.');
            return;
        }
       
        try {
            $maxReferralNumber = $this->getMaxReferralLimitNumber($commissionTypeId);
        } catch (Gpf_DbEngine_NoRowException $e) { 
            $context->debug('MaxCommission limit per referral is not defined. Ended.');
            return;
        }
        
        if ($maxReferralNumber == '' || $maxReferralNumber <= -1) {
            $context->debug('MaxCommission limit is not set.');
            return;
        }

        try {
            $maxReferralTimePeriod = $this->getMaxReferralLimitTimePeriod($commissionTypeId);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $maxReferralTimePeriod = -1;
        }

        $numberOfCommission = $this->getNumberOfAllTransactionPerReferralFromContext($context, $commissionTypeId, $maxReferralTimePeriod);
        if ($numberOfCommission < $maxReferralNumber) {
            $context->debug('MaxCommission per referral limit is not full ('.$numberOfCommission.'/ '.$maxReferralNumber.'). Saved. Ended.');
            return;
        }

        $context->setDoCommissionsSave(false);
        $context->debug('MaxCommission per referral is full (' . $numberOfCommission . '). Commission will be NOT SAVED. Ended.');
    }

    public function setSaveClickCommission(Pap_Contexts_Click $context) {
        $context->debug('MaxCommission per referral checking.');
        $commissionTypeId = $this->getCommissionTypeId($context);
        if ($commissionTypeId == null) {
            $context->debug('MaxCommission Commission type is null. Ended.');
            return;
        }

        try {
            $maxReferralNumber = $this->getMaxReferralLimitNumber($commissionTypeId);
        } catch (Gpf_DbEngine_NoRowException $e) { 
            $context->debug('MaxCommission limit per referral is not defined. Ended.');
            return;
        }

        if ($maxReferralNumber == '' || $maxReferralNumber <= -1) {
            $context->debug('MaxCommission limit is not set.');
            return;
        }

        try {
            $maxReferralTimePeriod = $this->getMaxReferralLimitTimePeriod($commissionTypeId);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $maxReferralTimePeriod = -1;
        }

        $numberOfCommission = $this->getNumberOfAllTransactionPerClickCommission($context->getUserObject()->getId(), $commissionTypeId, $maxReferralTimePeriod);
        if ($numberOfCommission < $maxReferralNumber) {
            $context->debug('MaxCommission per referral limit is not full ('.$numberOfCommission.'/ '.$maxReferralNumber.'). Saved. Ended.');
            return;
        }

        $context->setDoCommissionsSave(false);
        $context->debug('MaxCommission per referral is full (' . $numberOfCommission . '). Commission will be NOT SAVED. Ended.');
    }
    
    protected function getCommissionTypeId(Pap_Contexts_Tracking $context) {
        $commissionType = $context->getCommissionTypeObject();
        if ($commissionType == null) {
            return null;
        }
        return $commissionType->getId();   
    }
    
    protected function getMaxReferralLimitTimePeriod($commissionTypeId) {
        return Pap_Db_Table_CommissionTypeAttributes::getInstance()->getCommissionTypeAttribute(
            $commissionTypeId, self::MAX_COMMISSIONS_PER_REFERRAL_PERIOD)->getValue();  
    }

    protected function getMaxReferralLimitNumber($commissionTypeId) {
        return Pap_Db_Table_CommissionTypeAttributes::getInstance()->getCommissionTypeAttribute(
            $commissionTypeId, self::MAX_COMMISSIONS_PER_REFERRAL_NUMBER)->getValue();
    }

    protected function getNumberOfAllTransactionPerReferralFromContext(Pap_Contexts_Action $context, $commissionTypeId, $maxReferralTimePeriod) {
       return $this->getNumberOfAllTransactionPerReferral($context->getProductIdFromRequest(), 
            $context->getUserObject()->getId(), $commissionTypeId, $maxReferralTimePeriod);
    }
    
    private function getNumberOfAllTransactionPerReferral($referralId, $affiliateId, $commissionTypeId, $timePeriod = -1) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('COUNT('.Pap_Db_Table_Transactions::ORDER_ID.')', 'numberOfAll');
        $select->from->add(Pap_Db_Table_Transactions::getName(), 'transactions');
        $select->where->add('transactions.'.Pap_Db_Table_Transactions::PRODUCT_ID,
            '=', $referralId);
        $select->where->add('transactions.'.Pap_Db_Table_Transactions::USER_ID,
            '=', $affiliateId);
        $select->where->add('transactions.'.Pap_Db_Table_Transactions::COMMISSIONTYPEID,
            '=', $commissionTypeId); 
        if ($timePeriod > 0) {
            $select->where->add('transactions.'.Pap_Db_Table_Transactions::DATE_INSERTED,
                '>', 
            Gpf_Common_DateUtils::getDateTime(
                Gpf_Common_DateUtils::getTimestamp(Gpf_Common_DateUtils::now())-$timePeriod)
            );
        }
        $numberOfAll = $select->getOneRow()->get('numberOfAll');
        return $numberOfAll;
    }
    
    protected function getNumberOfAllTransactionPerClickCommission($userId, $commissionTypeId, $timePeriod = -1) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('SUM('.Pap_Db_Table_Transactions::CLICK_COUNT.')', 'numberOfAll');
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::USER_ID, '=', $userId);
        $select->where->add(Pap_Db_Table_Transactions::COMMISSIONTYPEID, '=', $commissionTypeId);
        if ($timePeriod > 0) {
            $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED,
                '>', 
            Gpf_Common_DateUtils::getDateTime(Gpf_Common_DateUtils::getTimestamp(Gpf_Common_DateUtils::now())-$timePeriod));
        }
        return $select->getOneRow()->get('numberOfAll');
    }
    
    private function getCommissionsAttributeWithDefaultValue($commissionTypeId, $name, $defaultValue) {
    	$commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
    	try {
    		return $commTypeAttr->getCommissionTypeAttribute($commissionTypeId, $name)->getValue();
    	} catch (Gpf_DbEngine_NoRowException $e) {
    		$commTypeAttr->setCommissionTypeAttributeValue($commissionTypeId, $name, $defaultValue);
    		return $defaultValue;
    	}
    }
}
?>
