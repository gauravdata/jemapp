<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
abstract class Pap_Features_Coupon_CouponsBaseTask extends Gpf_Tasks_LongTask {

    const MAX_REGENERATE_CODE_COUNT = 5;

    /**
     * @var Gpf_Rpc_Form
     */
    protected $form;
    protected $affiliateID;
    protected $affiliateCount;
    protected $campaignId;
    protected $campaignType;

    public function __construct(Gpf_Rpc_Form $form) {
        $this->form = $form;
        $this->init();
    }

    public function getName() {
        return $this->_('Create coupons');
    }

    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    protected function execute() {
        $this->affiliateID = Gpf_DbEngine_Row::NULL;
        $this->affiliateCount = $this->initAffiliateCount();
    }

    protected abstract function getProgressInfo();

    protected abstract function onConstraintException(Pap_Db_Coupon $coupon);

    /**
     * @return String
     */
    protected abstract function getOnFailMessage();

    protected function createCoupon($couponNumber, $code = Gpf_DbEngine_Row::NULL) {
        if ($this->isPending($couponNumber, $this->getProgressInfo())) {
            $this->generateCoupon($code);
            $this->setDone();
        }
    }

    protected function generateCoupon($code) {
        $coupon = new Pap_Db_Coupon();
        $this->form->fill($coupon);
        $coupon->setBannerID($this->form->getFieldValue(Pap_Features_Coupon_CreateCoupons::BANNER_ID));
        $coupon->setStatus(Pap_Common_Constants::STATUS_APPROVED);
        $coupon->setCode($code);
        $coupon->setUserID($this->affiliateID);
        $this->insertCoupon($coupon);
    }

    protected function setAffiliateID($couponNumber) {
        if ($this->form->getFieldValue(Pap_Features_Coupon_CreateCoupons::COUPON_ASSIGMENT) == 'A' &&
        $couponNumber % $this->form->getFieldValue(Pap_Features_Coupon_CreateCoupons::COUPONS_PER_AFF) == 0) {
            if (($affiliateNumber = floor($couponNumber /
            $this->form->getFieldValue(Pap_Features_Coupon_CreateCoupons::COUPONS_PER_AFF))) < $this->affiliateCount) {
                $this->affiliateID = $this->getAffiliateID($affiliateNumber);
                return;
            }
            $this->affiliateID = Gpf_DbEngine_Row::NULL;
        }
    }

    protected function getAffiliateID($offset) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Users::ID, '', 'pu');
        if ($this->campaignType != Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC) {
            $selectBuilder->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
            $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
                 'cg.'.Pap_Db_Table_CommissionGroups::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
            $selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu',
                 'pu.'.Pap_Db_Table_Users::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID);
            $selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u',
                 'u.'.Gpf_Db_Table_Users::ID.'=pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
            $selectBuilder->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $this->campaignId);
        } else {
            $selectBuilder->from->add(Pap_Db_Table_Users::getName(), 'pu');
            $selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u',
                 'u.'.Gpf_Db_Table_Users::ID.'=pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
            $selectBuilder->where->add('pu.' . Pap_Db_Table_Users::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
            $selectBuilder->where->add('u.'.Gpf_Db_Table_Users::STATUS, '=', Gpf_Db_User::APPROVED);
            $selectBuilder->where->add('pu.'.Pap_Db_Table_Users::DELETED, '=', Gpf::NO);
        }
        $selectBuilder->where->add('pu.' . Pap_Db_Table_Users::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
        $selectBuilder->where->add('u.'.Gpf_Db_Table_Users::STATUS, '=', Gpf_Db_User::APPROVED);
        $selectBuilder->where->add('pu.'.Pap_Db_Table_Users::DELETED, '=', Gpf::NO);
        $selectBuilder->limit->set($offset, 1);
        return $selectBuilder->getOneRow()->get(Pap_Db_Table_Users::ID);
    }
    
    protected function init() {
        $banner = new Pap_Db_Banner();
        $banner->setId($this->form->getFieldValue(Pap_Features_Coupon_CreateCoupons::BANNER_ID));
        try {
            $banner->load();
            $this->campaignId = $banner->getCampaignId();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->campaignId = null;
        }

        $campaign = new Pap_Db_Campaign();
        $campaign->setId($this->campaignId);
        try {
            $campaign->load();
            $this->campaignType = $campaign->getCampaignType();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->campaignType = null;
        }
    }

    protected function initAffiliateCount() {
        if ($this->campaignType != Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC) {
            return Pap_Db_Table_UserInCommissionGroup::getUsersInCommissionGroupCount($this->campaignId);
        }
        return Pap_Db_Table_Users::getAffiliateCount();
    }

    protected function insertCoupon(Pap_Db_Coupon $coupon) {
        for ($i = 0; $i < self::MAX_REGENERATE_CODE_COUNT; $i++) {
            try {
                $coupon->insert();
                return;
            } catch (Gpf_DbEngine_Row_ConstraintException $e) {
                $this->onConstraintException($coupon);
            } catch (Gpf_DbEngine_DuplicateEntryException $e) {
                $coupon->generateCouponID();
            }
        }
        throw new Gpf_Exception($this->getOnFailMessage());
    }
}
?>
