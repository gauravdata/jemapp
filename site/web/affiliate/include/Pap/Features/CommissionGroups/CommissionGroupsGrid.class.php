<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ExistingExportsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Pap_Features_CommissionGroups_CommissionGroupsGrid extends Gpf_View_GridService {
    
    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_CommissionGroups::NAME, $this->_('Name'), true);
        $this->addViewColumn('commissionOverview', $this->_('Commission overview'), true);
        $this->addViewColumn('affiliatesCount', $this->_('Count of affiliates'), true);
        $this->addViewColumn(self::ACTIONS, $this->_('Actions'));
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_CommissionGroups::ID);
        $this->addDataColumn(Pap_Db_Table_CommissionGroups::NAME);
        $this->addDataColumn(Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
        $this->addDataColumn('affiliatesCount', '(' . $this->getAffiliatesCountSelect()->toString() . ')');
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_CommissionGroups::NAME, '', 'A');
        $this->addDefaultViewColumn('commissionOverview', '', 'N');
        $this->addDefaultViewColumn('affiliatesCount', '', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
    }
    
    protected function buildFrom(){
        $this->_selectBuilder->from->add(Pap_Db_Table_CommissionGroups::getName(), 'cg');
    }
    
    protected function buildWhere() {
        $this->_selectBuilder->where->add(Pap_Db_Table_CommissionGroups::IS_DEFAULT, '=', Gpf::NO);
        $this->_selectBuilder->where->add(Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $this->getCampaignId());
        parent::buildWhere();
    }
    
    /**
     *
     * @param Gpf_Data_RecordSet $inputResult
     * @return Gpf_Data_RecordSet
     */
    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
    	$inputResult->addColumn('commissionOverview', 'N');
    	
        $cTable = Pap_Db_Table_Commissions::getInstance();
        $rsCommissions = $cTable->getAllCommissionsInCampaign();

        foreach ($inputResult as $record) {
        	
            if($cTable->findCampaignInCommExistsRecords($record->get(Pap_Db_Table_Campaigns::ID),
                $rsCommissions)) {
                $record->set('commissionOverview', $cTable->getCommissionsDescription($record->get(Pap_Db_Table_Campaigns::ID),
                    $rsCommissions, $record->get('id')));
            }
        }

        return $inputResult;
    }
    
    private function getCampaignId() {
        if ($this->_params->exists('campaignid')) {
            return $this->_params->get('campaignid');
        }

        throw new Gpf_Exception($this->_('Missing campaign id'));
    }
    
    /**
     *
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function getAffiliatesCountSelect() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('COUNT(ucg.'.Pap_Db_Table_UserInCommissionGroup::ID.')');
        $select->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
        $select->where->add('ucg.' . Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID, '=', 'cg.'.Pap_Db_Table_CommissionGroups::ID, 'AND', false);
        $select->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'u', 'ucg.userid=u.userid');
        $select->where->add('u.rtype', '=', Pap_Application::ROLETYPE_AFFILIATE);
        return $select;
    }
    
    /**
     * @service commission_group read
     *
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
}
?>
