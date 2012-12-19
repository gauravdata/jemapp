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
class Pap_Features_CampaignsCategories_CampaignInCategoriesGrid extends Gpf_View_GridService {
    
    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_HierarchicalDataNodes::NAME, $this->_('Name'), true);
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn('ni.'.Gpf_Db_Table_HierarchicalDataNodes::CODE);
        $this->addDataColumn(Gpf_Db_Table_HierarchicalDataNodes::CODE, 'ni.' . Gpf_Db_Table_HierarchicalDataNodes::CODE);
        $this->addDataColumn(Gpf_Db_Table_HierarchicalDataNodes::NAME, 'ni.'.Gpf_Db_Table_HierarchicalDataNodes::NAME);
        $this->addDataColumn('depth', '(COUNT(p.'.Gpf_Db_Table_HierarchicalDataNodes::NAME.') - 1)');
        $this->addDataColumn('selected', "IF (c.campaignid is null, 'N', 'Y')");
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn('selected', '');
        $this->addDefaultViewColumn(Gpf_Db_Table_HierarchicalDataNodes::NAME, '');
    }
    
    protected function buildFrom(){
        $this->_selectBuilder->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName(), 'p');
        $this->_selectBuilder->from->add(Gpf_Db_Table_HierarchicalDataNodes::getName(), 'ni');
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_CampaignsInCategory::getName(), 'c', 'c.categoryid = ni.code');
    }
    
    protected function buildWhere() {
        $this->_selectBuilder->where->add('ni.' . Gpf_Db_Table_HierarchicalDataNodes::LFT, 'BETWEEN', 'p.'.Gpf_Db_Table_HierarchicalDataNodes::LFT.' AND p.'.Gpf_Db_Table_HierarchicalDataNodes::RGT, 'AND', false);
        $this->_selectBuilder->where->add('ni.'.Gpf_Db_Table_HierarchicalDataNodes::TYPE,'=',Pap_Features_CampaignsCategories_Main::CAMPAIGNS_CATEGORIES_HIERARCHICAL_DATE_TYPE);
        $this->_selectBuilder->where->add('p.'.Gpf_Db_Table_HierarchicalDataNodes::TYPE,'=',Pap_Features_CampaignsCategories_Main::CAMPAIGNS_CATEGORIES_HIERARCHICAL_DATE_TYPE);
        $campaignFilter = $this->filters->getFilter("campaignid");
        if (count($campaignFilter) > 0) {
            $this->_selectBuilder->where->add('c.'.Pap_Db_Table_CampaignsInCategory::CAMPAIGNID,'=',$campaignFilter[0]->getValue());
        }
        parent::buildWhere();
    }
    
    protected function buildGroupBy() {
        $this->_selectBuilder->groupBy->add('ni.' . Gpf_Db_Table_HierarchicalDataNodes::NAME);
        $this->_selectBuilder->groupBy->add('ni.' . Gpf_Db_Table_HierarchicalDataNodes::LFT);
    }
    
    protected function buildOrder() {
        $this->_selectBuilder->orderBy->add('ni.'.Gpf_Db_Table_HierarchicalDataNodes::LFT);
    }

    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
        $outputResult = new Gpf_Data_RecordSet();
        $outputResult->setHeader($inputResult->getHeader());
        
        $tree = new Pap_Features_CampaignsCategories_Tree(false);
        foreach ($inputResult as $record) {
            if ($record->get('code')=='0' || $record->get('selected')=='N') {
                continue;
            }
            
            $newRecord = new Gpf_Data_Record($inputResult->getHeader());
            $newRecord->add('name', $tree->getBreadcrumb($record->get('id'), ' > '));
            $newRecord->add('id', $record->get('id'));
            
            $outputResult->add($newRecord);
        }
        return $outputResult;
    }
    
     /**
     * @service campaigns_categories read
     *
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
}
?>
