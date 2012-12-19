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
class Pap_Features_Coupon_GenerateCouponsTask extends Pap_Features_Coupon_CouponsBaseTask {

    /**
     * @var Gpf_Common_CodeUtils_CodeGenerator
     */
    private $generator;

    protected function execute() {
        parent::execute();
        $this->generator = new Gpf_Common_CodeUtils_CodeGenerator(
        $this->form->getFieldValue(Pap_Features_Coupon_CreateCoupons::COUPON_FORMAT));
        for ($couponNumber = 0; $couponNumber < $this->form->getFieldValue(
        Pap_Features_Coupon_CreateCoupons::COUPONS_COUNT); $couponNumber++) {
            $this->setAffiliateID($couponNumber);
            $this->createCoupon($couponNumber);
        }
    }

    protected function getProgressInfo() {
        return $this->_('Generate coupons');
    }

    protected function onConstraintException(Pap_Db_Coupon $coupon) {
        $coupon->setCode($this->generator->generate());
    }

    protected function getOnFailMessage() {
        return $this->_('Generating coupons was stopped, because coupon format is too small');
    }

    protected function insertCoupon(Pap_Db_Coupon $coupon) {
        $coupon->setCode($this->generator->generate());
        parent::insertCoupon($coupon);
    }
}
?>
