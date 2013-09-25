<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
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
class Pap_Affiliates_Promo_CampaignsGrid extends Pap_Merchants_Campaign_CampaignsGrid {

    protected function initViewColumns() {
        $this->addViewColumn('id', $this->_("ID"), true);
        $this->addViewColumn('name', $this->_("Name"), true);
        $this->addViewColumn('commissionsdetails', $this->_('Commissions'), 'A');
        $this->addViewColumn('rstatus', $this->_("Status"), true);
        $this->addViewColumn('dateinserted', $this->_("Date added"), true, Gpf_View_ViewColumn::TYPE_DATETIME);

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Affiliates.Campaigns.initViewColumns', $this);
    }

    protected function initDataColumns() {
        parent::initDataColumns();
        $this->addDataColumn('banners', 'COUNT(b.bannerid)');
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('name', '300', 'N');
        $this->addDefaultViewColumn('commissionsdetails', '50', 'N');
        $this->addDefaultViewColumn('rstatus', '50', 'N');

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Affiliates.Campaigns.initDefaultView', $this);
    }

    protected function isAddCampaign(Gpf_Data_Record $campaign) {
        $campaign->add('affstatus', 'A');
        if ($campaign->get('commissionsexist') != Gpf::NO) {
            try {
                Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CampaignsGrid.afterExecute', $campaign);
                return true;
            } catch (Gpf_Exception $e) {
            }
        }
        return false;
    }

    protected function initResult() {
        $result = parent::initResult();
        $result->addColumn('affstatus', 'A');
        return $result;
    }

    protected function buildFrom() {
        parent::buildFrom();
        $onCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $onCondition->add('c.'.Pap_Db_Table_Campaigns::ID, '=', 'b.'.Pap_Db_Table_Banners::CAMPAIGN_ID, 'AND', false);
        $onCondition->add('b.'.Pap_Db_Table_Banners::STATUS, '=', Pap_Db_Banner::STATUS_ACTIVE);
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Banners::getName(), 'b', $onCondition->toString());
    }

    protected function buildGroupBy() {
        $this->_selectBuilder->groupBy->add('c.campaignid');
    }

    protected function buildOrder() {
        if($this->_sortColumn) {
            if (array_key_exists($this->_sortColumn, $this->dataColumns)) {
                $this->_selectBuilder->orderBy->add($this->_sortColumn, $this->_sortAsc);
                return;
            }
        }
        $this->_selectBuilder->orderBy->add('rorder', true);
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add('c.'.Pap_Db_Table_Campaigns::STATUS, '<>', Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED_INVISIBLE);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Affiliates.Campaigns.buildWhere', new Pap_Affiliates_Promo_SelectBuilderCompoundFilter($this->_selectBuilder, $this->filters));
    }

    /**
     * @service campaign read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service campaign read
     * @return Gpf_Rpc_Serializable
     */
    public function getLongDescription(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        $search = $data->getFilters()->getFilter("id");
        if (sizeof($search) == 1) {
            $id = $search[0]->getValue();
        }

        $campaign = new Pap_Db_Campaign();
        $campaign->setId($id);
        $campaign->load();

        $longDescription = $campaign->getLongDescription();
        if($longDescription == '') {
            $longDescription = $this->_('Empty');
        }
        $data->setValue("longdescription", $longDescription);
        return $data;
    }

    /**
     * @service campaign export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }

    protected function getCommissionGroupId(Gpf_Data_Record $campaignRecord) {
        $campaign = new Pap_Db_Campaign();
        $campaign->fillFromRecord($campaignRecord);
        $commissionGroupId = $campaign->checkUserIsInCampaign(Gpf_Session::getAuthUser()->getPapUserId());
         
        if ($commissionGroupId != false) {
            return $commissionGroupId;
        }
        return null;
    }

}
?>
