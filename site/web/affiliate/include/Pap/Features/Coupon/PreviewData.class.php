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
class Pap_Features_Coupon_PreviewData extends Gpf_Object {

    /**
     * @service coupon export
     * @param bannerid
     * @return Gpf_Rpc_Serializable
     */
    public function exportCodes(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $coupon = new Pap_Features_Coupon_Coupon();
        $coupon->setId($form->getFieldValue('bannerid'));
        $select = $coupon->getCodes(Gpf_Session::getAuthUser()->getPapUserId());
        $csvGenerator = new Gpf_Csv_GeneratorResponse('codes.csv', null, $select->getAllRows());
        return $csvGenerator->generateFile();
    }

    /**
     * @service coupon read
     * @param Id
     * @return Gpf_Rpc_Data
     */
    public function loadCouponsCount(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $coupon = new Pap_Features_Coupon_Coupon();
        $coupon->setId($data->getId());
        try {
            $coupon->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return $data;            
        }
        $couponsUsed = $coupon->getCouponsCount(Gpf_Session::getAuthUser()->getPapUserId());        
        $data->setValue('usedcoupons', $couponsUsed);
        $data->setValue('availablecoupons', $this->computeAvailableCoupons($coupon, $couponsUsed));
        $data->setValue('validcoupons', $coupon->getValidCouponsCount(Gpf_Session::getAuthUser()->getPapUserId()));

        return $data;
    }

    /**
     * @service coupon write
     * @param Id
     * @return Gpf_Rpc_Action
     */
    public function assignCoupon(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to assign coupon'));
        $action->setInfoMessage($this->_('Coupon was successfully assigned'));

        $coupon = new Pap_Features_Coupon_Coupon();
        $coupon->setId($action->getParam('id'));
        try {
            $unusedCoupon = $coupon->getUnusedCoupon();
            $unusedCoupon->setUserID(Gpf_Session::getAuthUser()->getPapUserId());
            $unusedCoupon->save();
            $action->addOk();
        } catch (Gpf_Exception $e) {
            $action->addError();
        }

        return $action;
    }
    
    private function computeAvailableCoupons(Pap_Features_Coupon_Coupon $coupon, $couponsUsed) {
        $couponsCount = $coupon->getCouponsCount();
        if ($couponsCount - $coupon->getData2() + $couponsUsed >= 0) {
            return $coupon->getData2() - $couponsUsed;
        }
        return $couponsCount;
    }
}
?>
