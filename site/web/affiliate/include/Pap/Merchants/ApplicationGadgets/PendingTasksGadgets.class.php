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
class Pap_Merchants_ApplicationGadgets_PendingTasksGadgets extends Gpf_Object {
    const PENDING = "P";
	
    /**
     *
     * @service pending_task read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        
        $data->setValue("pendingAffiliates", $this->getPendingAffiliatesCount());
        $data->setValue("pendingDirectLinks", $this->getPendingDirectLinksCount());
        
        $transactionsInfo = $this->getPendingTransactionsInfo();
        
        $data->setValue("pendingCommissions", $transactionsInfo->get("pendingCommissions"));
        $data->setValue("totalCommissions", $transactionsInfo->get("totalCommissions"));
        $data->setValue("unsentEmails", $this->getUnsetEmails());

        return $data;
    }
    
    private function getPendingAffiliatesCount() {
    	$select = new Gpf_SqlBuilder_SelectBuilder();
    	$select->select->add(Gpf_Db_Table_Users::ID);
    	$select->from->add(Gpf_Db_Table_Users::getName());
    	$select->where->add(Gpf_Db_Table_Users::STATUS, "=", self::PENDING);
    	$select->where->add(Gpf_Db_Table_Users::ROLEID, "=", Pap_Application::DEFAULT_ROLE_AFFILIATE);
    	$result = $select->getAllRows();
    	
    	return $result->getSize();
    }
    
    private function getPendingTransactionsInfo() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add("SUM(IF(".Pap_Db_Table_Transactions::R_STATUS." = '".self::PENDING."',1,0))", "pendingCommissions");
        $select->select->add("SUM(IF(".Pap_Db_Table_Transactions::R_STATUS." = '".self::PENDING."',".
        Pap_Db_Table_Transactions::COMMISSION.",0))", "totalCommissions");
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $result = $select->getOneRow();
        
        return $result;
    }
    
    private function getPendingDirectLinksCount() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Pap_Db_Table_DirectLinkUrls::ID);
        $select->from->add(Pap_Db_Table_DirectLinkUrls::getName());
        $select->where->add(Pap_Db_Table_DirectLinkUrls::STATUS, "=", self::PENDING);
        $result = $select->getAllRows();
        
        return $result->getSize();
    }
    
    private function getUnsetEmails() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add("COUNT(".Gpf_Db_Table_MailOutbox::ID.")", "unsentEmails");
        $select->from->add(Gpf_Db_Table_MailOutbox::getName());
        $select->where->add(Gpf_Db_Table_MailOutbox::STATUS, "=", "P");
        $result = $select->getOneRow();
        
        return $result->get("unsentEmails");
    }
}

?>
