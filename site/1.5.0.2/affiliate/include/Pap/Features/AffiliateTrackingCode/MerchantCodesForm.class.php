<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Features_AffiliateTrackingCode_MerchantCodesForm extends Pap_Features_AffiliateTrackingCode_CodesForm {       
        
    /**
     * @service affiliate_tracking_code delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
    	return parent::deleteRows($params);
    }
    
    /**
     * @service affiliate_tracking_code write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
    	return parent::saveFields($params);
    }
    
    /**
     *
     * @service affiliate write
     * @param ids, status
     * @return Gpf_Rpc_Action
     */
    public function changeStatus(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Selected affiliate(s) status is changed"));
        $action->setErrorMessage($this->_("Failed to change status for selected affiliate(s)"));

        foreach ($action->getIds() as $id){
            try {
                $trackingCode = $this->createDbRowObject();
                $trackingCode->setId($id);
                $trackingCode->load();
                if ($trackingCode->getStatus() == $action->getParam("status")) {
                    continue;
                }
                $trackingCode->setStatus($action->getParam("status"));
                $trackingCode->update();
                $action->addOk();
            } catch(Gpf_DbEngine_NoRowException $e) {
                $action->addError();
            }
        }

        return $action;
    }
}
?>
