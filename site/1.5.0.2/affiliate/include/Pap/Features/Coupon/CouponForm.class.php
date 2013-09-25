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
class Pap_Features_Coupon_CouponForm extends Gpf_View_FormService {

    /**
     * @service coupon delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }

    /**
     * @service coupon write
     * @param ids, status
     * @return Gpf_Rpc_Action
     */
    public function changeStatus(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Status successfully set to selected coupon(s)"));
        $action->setErrorMessage($this->_("Failed to set status selected coupon(s)"));

        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_Coupons::getName());
        $update->set->add(Pap_Db_Table_Coupons::STATUS, $action->getParam("status"));

        foreach ($action->getIds() as $id){
            $update->where->add(Pap_Db_Table_Coupons::ID, "=", $id, "OR");
        }

        try {
            $update->execute();
            $action->addOk();
        } catch(Gpf_DbEngine_NoRowException $e) {
            $action->addError();
        }

        return $action;
    }
    
    /**
     *
     * @service coupon write
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @return Pap_Db_Coupon
     */
    protected function createDbRowObject() {
        return new Pap_Db_Coupon();
    }
}
?>
