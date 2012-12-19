<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro 
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Merchants_Campaign_CommissionGroups extends Gpf_Object {
    
    /**
     * @service commission read
     * 
     * @param $campaignid
     * @return Gpf_Data_RecordSet <commissiongroupid, name, isdefault>
     */
    public function loadCommissionGroups(Gpf_Rpc_Params $params) {
        $campaignId = $params->get("campaignid"); 
        if($campaignId == "") {
          throw new Gpf_Exception($this->_("loadCommissionGroups: campaignId is empty"));
        }
        
        $commissionsGroupTable = Pap_Db_Table_CommissionGroups::getInstance();
        $groups = $commissionsGroupTable->getAllCommissionGroups($campaignId);
        
        if($groups->getSize() == 0) {
          $this->insertDefaultCommissionGroup($campaignId);
          $groups = $commissionsGroupTable->getAllCommissionGroups($campaignId);
        }
        
        return $groups;
    }
    
    public function insertDefaultCommissionGroup($campaignId) {
      $cg = new Pap_Db_CommissionGroup();
      $cg->set("campaignid", $campaignId);
      $cg->set("isdefault", Gpf::YES);
      $cg->set("name", "Default commission group");
      $cg->insert();
    }
}
?>
