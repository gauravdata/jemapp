<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
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
class Pap_Features_CampaignsCategories_Main extends Gpf_Plugins_Handler {
	
    const CAMPAIGNS_CATEGORIES_HIERARCHICAL_DATE_TYPE = 'cmpcat';
    
    public static function getHandlerInstance() {
        return new Pap_Features_CampaignsCategories_Main();
    }
    
    public function addToMenu(Gpf_Menu $menu) {
        $menu->getItem('Campaigns-Overview')->addItem('Campaigns-Categories', $this->_('Campaigns Categories'));
    }
    
    public function addCategoryFilterToMerchantCategoryList(Pap_Affiliates_Promo_SelectBuilderCompoundFilter $compound) {
        $selectBuilder = $compound->getSelectBuilder();
        $filters = $compound->getFilters();
        $categoryFilter = $filters->getFilter("categoryid");
        if (count($categoryFilter) > 0) {
            if ($categoryFilter[0]->getValue()=="FILTER_SELECT_ALL") {
                $selectBuilder->from->addLeftJoin(Pap_Db_Table_CampaignsInCategory::getName(), 'cic',
                    'c.'.Pap_Db_Table_Campaigns::ID.'=cic.'.Pap_Db_Table_CampaignsInCategory::CAMPAIGNID);
                $selectBuilder->where->add('cic.'.Pap_Db_Table_CampaignsInCategory::CATEGORYID,'!=',null);
                $selectBuilder->groupBy->add('c.'.Pap_Db_Table_Campaigns::ID);
            } else {
                $selectBuilder->from->addLeftJoin(Pap_Db_Table_CampaignsInCategory::getName(), 'cic',
                    'c.'.Pap_Db_Table_Campaigns::ID.'=cic.'.Pap_Db_Table_CampaignsInCategory::CAMPAIGNID.
                    ' AND cic.'.Pap_Db_Table_CampaignsInCategory::CATEGORYID.' IN ('.$categoryFilter[0]->getValue().')');
                $selectBuilder->where->add('cic.'.Pap_Db_Table_CampaignsInCategory::CATEGORYID,'in',preg_split('/,/',$categoryFilter[0]->getValue()));
                $selectBuilder->groupBy->add('c.'.Pap_Db_Table_Campaigns::ID);
            }
        }
    }
}
?>
