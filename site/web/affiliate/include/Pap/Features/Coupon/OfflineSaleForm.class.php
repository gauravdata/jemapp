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
class Pap_Features_Coupon_OfflineSaleForm extends Gpf_Object {

    /**
     * @service transaction add
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $this->setValidators($form);
        if ($form->validate()) {
            $this->createSale($form);
        }
        return $form;
    }

    /**
     * @service coupon read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(Pap_Db_Table_Coupons::CODE, null);

        $coupon = new Pap_Db_Coupon();
        $coupon->setId($form->getFieldValue(Gpf_View_FormService::ID));
        $coupon->setCode($form->getFieldValue(Gpf_View_FormService::ID));
        $banner = new Pap_Features_Coupon_Coupon();
        $affiliate = new Pap_Affiliates_User();

        try {
            $this->loadCoupon($coupon);
            if (!$coupon->isValid()) {
                return $form;
            }
            $banner->setPrimaryKeyValue($coupon->getBannerID());
            $banner->load();
            $affiliate->setPrimaryKeyValue($coupon->getUserID());
            $affiliate->load();
            $form->addField('couponid', $coupon->getId());
            $form->addField('couponcode', $coupon->getCode());
            $form->addField('name', $banner->getName());
            $form->addField('description', $banner->getData1());
            $this->fillFromUser($form, $affiliate);
            
            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.OfflineSaleForm.load', $form);
            
        } catch (Gpf_Exception $e) {
        }

        return $form;
    }

    private function fillFromUser(Gpf_Rpc_Form $form, Pap_Affiliates_User $user) {
        $userData = new Gpf_Data_RecordSet();
        $userData->setHeader(array('userid', 'username','firstname', 'lastname'));
        $data = $userData->createRecord();
        $data->add('userid', $user->getId());
        $data->add('username', $user->getUserName());
        $data->add('firstname', $user->getFirstName());
        $data->add('lastname', $user->getLastName());
        $userData->add($data);
        $form->setField('userid', null, $userData->toObject());
    }

    private function createSale(Gpf_Rpc_Form $form) {
        $saleTracker = new Pap_Tracking_ActionTracker();
        $sale = $saleTracker->createSale();
        $sale->setTotalCost($form->getFieldValue(Pap_Db_Table_Transactions::TOTAL_COST));
        $sale->setOrderID($form->getFieldValue(Pap_Db_Table_Transactions::ORDER_ID));
        $sale->setProductID($form->getFieldValue(Pap_Db_Table_Transactions::PRODUCT_ID));
        $sale->setData1($form->getFieldValue(Pap_Db_Table_Transactions::DATA1));
        $sale->setData2($form->getFieldValue(Pap_Db_Table_Transactions::DATA2));
        $sale->setData3($form->getFieldValue(Pap_Db_Table_Transactions::DATA3));
        $sale->setData4($form->getFieldValue(Pap_Db_Table_Transactions::DATA4));
        $sale->setData5($form->getFieldValue(Pap_Db_Table_Transactions::DATA5));
        $sale->setCoupon($form->getFieldValue(Pap_Db_Table_Coupons::CODE));
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.OfflineSaleForm.createSale', $sale);
        
        // For handle Affiliate Sale Tracking Codes, this feature write its codes to output
        ob_start();
        $saleTracker->register();
        ob_end_clean();

        $form->setInfoMessage($this->_('Sale was saved'));
    }

    private function loadRow(Gpf_DbEngine_Row $row, $rowName) {
        try {
            $row->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Gpf_Exception($this->_('%s with id %s not exist', $rowName, $row->getPrimaryKeyValue()));
        }
    }

    private function setValidators(Gpf_Rpc_Form $form) {
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), 'totalcost', $this->_('Total cost'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_NumberValidator(), 'totalcost');
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), 'orderid', $this->_('Order ID'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), 'productid', $this->_('Product ID'));
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     */
    private function loadCoupon(Pap_Db_Coupon $coupon) {
        try {
            $coupon->load();
            return;
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
        $coupon->loadFromCode();
    }
}
?>
