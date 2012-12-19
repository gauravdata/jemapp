<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ChannelsForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
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
class Pap_Features_Coupon_CreateCoupons extends Gpf_Object {

    const GENERATE = 'generate';
    const IMPORT = 'import';
    const SUCCESS = 'success';

    const AFFILIATES = 'affiliates';
    const COUPONS_COUNT = 'couponscount';
    const COUPONS_PER_AFF = 'couponsperaff';
    const COUPON_FORMAT = 'couponsformat';
    const COUPON_ASSIGMENT = 'couponassigment';
    const BANNER_ID = 'Id';
    const VALID_FROM = 'validfrom';
    const VALID_TO = 'validto';
    const MAX_USE_COUNT = 'maxusecount';
    const COUPONS_CODES = 'couponcodes';

    /**
     * @var Gpf_Rpc_Form
     */
    private $form;
    /**
     * @var Pap_Features_Coupon_CouponsBaseTask
     */
    private $task;

    /**
     * @service coupon read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $this->form = new Gpf_Rpc_Form($params);

        $affiliates = $this->getAffiliateCount();
        $this->form->addField(self::AFFILIATES, $affiliates);
        $this->form->addField(self::COUPONS_COUNT, round($affiliates * 1.1));
        $this->form->addField(self::COUPONS_PER_AFF, 1);

        return $this->form;
    }

    /**
     * @service coupon add
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function generate(Gpf_Rpc_Params $params) {
        $this->form = new Gpf_Rpc_Form($params);
        $this->setValidators(self::GENERATE);
        $this->task = new Pap_Features_Coupon_GenerateCouponsTask($this->form);
        $this->validateAndRun(self::GENERATE);
        return $this->form;
    }

    /**
     * @service coupon import
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function import(Gpf_Rpc_Params $params) {
        $this->form = new Gpf_Rpc_Form($params);
        $this->setValidators(self::IMPORT);
        $this->task = new Pap_Features_Coupon_ImportCouponsTask($this->form);
        $this->validateAndRun(self::IMPORT);
        return $this->form;
    }

    private function getAffiliateCount() {
        $banner = new Pap_Db_Banner();
        $banner->setId($this->form->getFieldValue(Pap_Features_Coupon_CreateCoupons::BANNER_ID));
        try {
            $banner->load();
            $campaignId = $banner->getCampaignId();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $campaignId = null;
        }

        $campaign = new Pap_Db_Campaign();
        $campaign->setId($campaignId);
        try {
            $campaign->load();
            $campaignType = $campaign->getCampaignType();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $campaignType = null;
        }

        if ($campaignType != Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC) {
            return Pap_Db_Table_UserInCommissionGroup::getUsersInCommissionGroupCount($campaignId);
        }
        return Pap_Db_Table_Users::getInstance()->getAffiliateCount();
    }

    private function validateAndRun($type) {
        if ($this->form->validate()) {
            try {
                $this->task->run();
            } catch (Gpf_Tasks_LongTaskInterrupt $e) {
                $this->form->setField(self::SUCCESS, Gpf::NO);
                $this->form->setInfoMessage($e->getMessage());
                return;
            } catch (Exception $e) {
                $this->task->forceFinishTask();
                $this->form->setField(self::SUCCESS, Gpf::YES);
                $this->form->setErrorMessage($this->_('Error during ' . $this->getActionName($type) . ' coupons') . ' (' . $e->getMessage() . ') ');
                return;
            }
            $this->form->setInfoMessage($this->_('Coupons are successfuly ' . $this->getActionName($type, true)));
            $this->form->setField(self::SUCCESS, Gpf::YES);
        }
    }

    private function setValidators($type) {
        if($this->form->getFieldValue("unlimitedValidity") == Gpf::YES) {
            $this->form->setField('validto', '2030-12-30');
        }
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator(), self::MAX_USE_COUNT, $this->_('Limit use'));
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::MAX_USE_COUNT);
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_DateValidator(), self::VALID_FROM, $this->_('Valid from'));
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::VALID_FROM);
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_DateValidator(), self::VALID_TO, $this->_('Valid to'));
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::VALID_TO);
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::COUPON_ASSIGMENT, $this->_('Coupon assigment'));
        if ($this->form->getFieldValue(self::COUPON_ASSIGMENT) == 'A') {
            $this->form->addValidator(new Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator(), self::COUPONS_PER_AFF, $this->_('Coupons to each affiliate'));
            $this->form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::COUPONS_PER_AFF);
        }
        if ($type == self::IMPORT) {
            $this->form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::COUPONS_CODES, $this->_('Coupon codes'));
            return;
        }
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_RegExpValidator('/^([^{},]*({[zZ9X]+})+[^{},]*)+$/', $this->_('Enter valid %s')), self::COUPON_FORMAT, $this->_('Coupon format'));
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::COUPON_FORMAT);
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator(), self::COUPONS_COUNT, $this->_('Number of coupons'));
        $this->form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::COUPONS_COUNT);
    }

    private function getActionName($type, $postfix = false) {
        if ($type == self::GENERATE) {
            return 'generate' . ($postfix ? 'd' : '');
        }
        return 'import' . ($postfix ? 'ed' : '');
    }
}

?>
