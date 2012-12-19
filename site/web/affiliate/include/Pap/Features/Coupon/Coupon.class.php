<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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
class Pap_Features_Coupon_Coupon extends Pap_Common_Banner {

	const TYPE_COUPON = 'C';
	const DEFAULT_DESIGN = '{$couponcode}';

	public function initValidators(Gpf_Rpc_Form $form) {
		if (!($form->existsField('isdesign') && $form->getFieldValue('isdesign') == Gpf::YES)) {
			$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::NAME, $this->_('name'));
			$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::STATUS, $this->_('status'));
			$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::SIZE, $this->_('size'));
			$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::DATA1, $this->_('description'));
			$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::DATA2, $this->_('maximum coupons per affiliate'));
			$form->addValidator(new Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator(), Pap_Db_Table_Banners::DATA2);
		}
	}

	/**
	 * Get first unused coupon
	 * @throws Gpf_DbEngine_NoRowException
	 * @return Pap_Db_Coupon
	 */
	public function getUnusedCoupon() {
		$select = $this->createCouponsSelect();
		$select->select->add(Pap_Db_Table_Coupons::ID);
		$select->limit->set(0, 1);

		$coupon = new Pap_Db_Coupon();
		$coupon->setPrimaryKeyValue($select->getOneRow()->get(Pap_Db_Table_Coupons::ID));
		$coupon->load();
		return $coupon;
	}

	/**
	 * If userID is set, then return user coupons count, else unused coupons count
	 * @param $userID
	 * @return $count
	 */
	public function getCouponsCount($userID = null) {
		$select = $this->createCouponsSelect($userID);
		$select->select->add('COUNT('.Pap_Db_Table_Coupons::ID.')', 'count');
		return $select->getOneRow()->get('count');
	}

    /**
     * If userID is set, then return valid user coupons count, else unused valid coupons count
     * @param $userID
     * @return $count
     */
    public function getValidCouponsCount($userID = null) {
        $select = $this->createCouponsSelect($userID);
        $select->select->addAll(Pap_Db_Table_Coupons::getInstance());
        $coupon = new Pap_Db_Coupon();
        $coupons = $coupon->loadCollectionFromRecordset($select->getAllRows());
        $validCouponsCount = 0;
        foreach ($coupons as $coupon) {
            if($coupon->isValid()) {
                $validCouponsCount++;
            }
        }
        return $validCouponsCount;
    }

	/**
	 * If userID is set, then return select with user coupon codes, else select with unused coupon codes
	 * @param $userID
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	public function getCodes($userID = null) {
		$select = $this->createCouponsSelect($userID);
		$select->select->add(Pap_Db_Table_Coupons::CODE);
		return $select;
	}

	public function getCouponText(Pap_Db_Coupon $coupon) {
		$couponFormat = $this->getData3();
		$couponFormat = str_replace('{$couponcode}', $coupon->getCode(), $couponFormat);
		$couponFormat = str_replace('{$couponid}', $coupon->getId(), $couponFormat);
		$couponFormat = str_replace('{$validfrom}', $coupon->get(Pap_Db_Table_Coupons::VALID_FROM), $couponFormat);
		$couponFormat = str_replace('{$validto}', $coupon->get(Pap_Db_Table_Coupons::VALID_TO), $couponFormat);
		$couponFormat = str_replace('{$limituse}', ($coupon->get(Pap_Db_Table_Coupons::MAX_USE_COUNT) == 0 ? $this->_('unlimited') : $coupon->get(Pap_Db_Table_Coupons::MAX_USE_COUNT)), $couponFormat);
		if (strstr($couponFormat, '{$barcodeimage}') !== false) {
			$barCode = new Gpf_BarCode_BarCode();
			$couponFormat = str_replace('{$barcodeimage}', $barCode->getLink($coupon->getId()), $couponFormat);
		}
		
		if (strstr($couponFormat, '{$qrcodeimage}') !== false) {
			$QrCode = new Gpf_QrCode_QrCode();
			$couponFormat = str_replace('{$qrcodeimage}', $QrCode->getLink($coupon->getCode()), $couponFormat);
		}
		
		return $couponFormat;
	}

	/**
	 * @param $userID
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	private function createCouponsSelect($userID = null) {
		$select = new Gpf_SqlBuilder_SelectBuilder();
		$select->from->add(Pap_Db_Table_Coupons::getName());
		$select->where->add(Pap_Db_Table_Coupons::BANNERID, '=', $this->getId());
		$select->where->add(Pap_Db_Table_Coupons::USERID, '=', $userID);
		return $select;
	}
}

?>
