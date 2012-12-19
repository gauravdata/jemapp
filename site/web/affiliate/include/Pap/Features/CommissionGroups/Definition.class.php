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

class Pap_Features_CommissionGroups_Definition extends Gpf_Plugins_Definition  {
    
    const CODE_NAME = 'CommissionGroups';
    
	public function __construct() {
		$this->codeName = self::CODE_NAME;
		$this->name = $this->_('Commission Groups');
		$this->description = $this->_('Commission groups allow you to set different commissions for different users for the same campaign.<br/><a href="%s" target="_blank">More help in our Knowledge Base</a>',Gpf_Application::getKnowledgeHelpUrl('features-and-functions/commission-groups.html'));
		$this->version = '1.0.0';
		$this->pluginType = self::PLUGIN_TYPE_FEATURE;
	}

	public function onDeactivate() {
		$this->transferUsers();
		$this->removeCommissionGroups();
	}
	
	private function transferUsers() {
		$update = new Gpf_SqlBuilder_UpdateBuilder();
		$update->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
		$update->from->addLeftJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
           'cg.'.Pap_Db_Table_CommissionGroups::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
        $update->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c',
           'cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID.'=c.'.Pap_Db_Table_Campaigns::ID);
		$update->set->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID, $this->getDefaultCommissionGroup(), false);	
        $update->where->add('cg.'.Pap_Db_Table_CommissionGroups::IS_DEFAULT, '=', Gpf::NO);
        
		$update->update();
	}
	
	private function  getDefaultCommissionGroup() {
		$select = new Gpf_SqlBuilder_SelectBuilder();
		$select->select->add(Pap_Db_Table_CommissionGroups::ID);
		$select->from->add(Pap_Db_Table_CommissionGroups::getName());
		$select->where->add(Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', 'c.campaignid', 'AND', false);
        $select->where->add(Pap_Db_Table_CommissionGroups::IS_DEFAULT, '=', Gpf::YES);
        $select->limit->set(0,1);
		
		return '('.$select->toString().')';
	}

	private function removeCommissionGroups() {
		$delete = new Gpf_SqlBuilder_DeleteBuilder();
		$delete->delete->add('cg');
		$delete->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
		$delete->from->addRightJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
           'ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID.'=cg.'.Pap_Db_Table_CommissionGroups::ID);
		$delete->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c',
           'cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID.'=c.'.Pap_Db_Table_Campaigns::ID);
		$delete->where->add('cg.'.Pap_Db_Table_CommissionGroups::IS_DEFAULT, '=', Gpf::NO);
		$delete->delete();
	}
}
?>
