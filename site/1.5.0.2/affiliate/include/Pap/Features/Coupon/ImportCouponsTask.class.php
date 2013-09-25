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
class Pap_Features_Coupon_ImportCouponsTask extends Pap_Features_Coupon_CouponsBaseTask {

    /**
     * @var array
     */
    private $codes;

    protected function execute() {
        parent::execute();
        $this->codes = preg_split('/[;,\n]/', $this->form->getFieldValue('couponcodes'));
        $couponNumber = 0;
        foreach ($this->codes as $code) {
            $this->setAffiliateID($couponNumber);
            $this->createCoupon($couponNumber, $code);
            $couponNumber++;
        }
    }

    protected function getProgressInfo() {
        return $this->_('Import coupons');
    }

    protected function onConstraintException(Pap_Db_Coupon $coupon) {
        throw new Gpf_Exception($this->_('Importing coupons was stopped, because coupon code %s already exists', $coupon->getCode()));
    }

    protected function getOnFailMessage() {
        return $this->_('Importing coupons was stopped, because coupons count is maximal');
    }
}
?>
