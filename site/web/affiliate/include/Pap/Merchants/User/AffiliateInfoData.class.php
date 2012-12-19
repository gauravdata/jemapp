<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_User_AffiliateInfoData extends Pap_Common_Overview_OverviewBase {

    /**
     *
     * @service affiliate_stats read
     * @param $data
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        $filters = $data->getFilters();
		$statsParams = new Pap_Stats_Params();
		$statsParams->initFrom($filters);				
		$transactions = new Pap_Stats_Transactions($statsParams);                

        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->from->add(Pap_Db_Table_Users::getName(), "pu");
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "gu.accountuserid = pu.accountuserid");
        $select->select->add("SUM(IF(pu.rtype = '". Pap_Application::ROLETYPE_AFFILIATE ."', 1, 0))", "countAffiliates");
        $select->select->add("SUM(IF(gu.rstatus = 'A', 1, 0))", "approvedAffiliates");
        $select->select->add("SUM(IF(gu.rstatus = 'P', 1, 0))", "pendingAffiliates");
        $filters->addTo($select->where);
        $select->where->add("pu.".Pap_Db_Table_Users::TYPE, "=", Pap_Application::ROLETYPE_AFFILIATE);        
        $row = $select->getOneRow();

        $data->setValue("countAffiliates", $this->checkNullValue($row->get("countAffiliates")));
        $data->setValue("approvedAffiliates", $this->checkNullValue($row->get("approvedAffiliates")));
        $data->setValue("pendingAffiliates", $this->checkNullValue($row->get("pendingAffiliates")));
        $data->setValue("totalSales", $transactions->getTotalCost()->getAll());
        $data->setValue("approvedCommissions", $transactions->getCommission()->getApproved());
        $data->setValue("pendingCommissions", $transactions->getCommission()->getPending());

        return $data;
    }
     
    /**
     * Load affiliate detail for affiliate manager
     *
     * @service affiliate read
     * @param $fields
     */
    public function affiliateDetails(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        $search = $data->getFilters()->getFilter("id");
        if (sizeof($search) == 1) {
            $id = $search[0]->getValue();
        }
        

        $user = new Pap_Affiliates_User();
        $user->setId($id);
        try {
            $user->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return $data;
        }
        $data->setValue("id", $user->getId());
        $data->setValue("name", $user->getFirstName()." ".$user->getLastName());
        $data->setValue("username", $user->getUserName());

        $formFields = $this->getUserFormFields();
        foreach($formFields as $record) {
            $code = $record->get('code');
            $data->setValue($code, $user->get($code));
        }

        return $data;
    }

    private function getUserFormFields() {
        $ffTable = Gpf_Db_Table_FormFields::getInstance();
        return $ffTable->getFieldsNoRpc("affiliateForm", array('M', 'O', 'R'));
    }
}

?>
