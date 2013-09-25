<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Banners.class.php 22243 2008-11-10 12:30:52Z mbebjak $
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
class Pap_Merchants_Banner_ListBox extends Gpf_Ui_RichListBoxService {
	
   /**
     * @service banner read
     * @param $id, $search, $from, $rowsPerPage
     * @return Gpf_Rpc_Object
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createSelectBuilder() {
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('b.'.Pap_Db_Table_Banners::ID, self::ID);
        $selectBuilder->select->add('b.'.Pap_Db_Table_Banners::NAME, self::VALUE);
        $selectBuilder->from->add(Pap_Db_Table_Banners::getName(), 'b');        
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'camp', 'b.'.Pap_Db_Table_Banners::CAMPAIGN_ID.'=camp.'.Pap_Db_Table_Campaigns::ID);
        
        $cond = new Gpf_SqlBuilder_CompoundWhereCondition();
        $cond->add('b.'.Pap_Db_Table_Banners::STATUS, '=', 'A');
        $cond->add('b.'.Pap_Db_Table_Banners::STATUS, '=', 'H','OR');
        $selectBuilder->where->addCondition($cond,'AND');
        $selectBuilder->where->add('camp.'.Pap_Db_Table_Campaigns::STATUS, 'IN',
        array(Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE, Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED));
        
        if ($this->params->get('campaignid') != '') {
            $selectBuilder->where->add('b.'.Pap_Db_Table_Banners::CAMPAIGN_ID, '=', $this->params->get('campaignid'));
        }
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannerListbox.getBannerSelect', $selectBuilder);
        
        $this->onBannerSelectBuilderCreated($selectBuilder);
        
        return $selectBuilder;
    }
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
    	$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('b.'.Pap_Db_Table_Banners::NAME, 'LIKE', '%'.$search.'%', 'OR');
        $condition->add('b.'.Pap_Db_Table_Banners::ID, 'LIKE', '%'.$search.'%', 'OR');
        $selectBuilder->where->addCondition($condition);
    }
    
    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
    	$selectBuilder->where->add('b.'.Pap_Db_Table_Banners::ID, '=', $id);
    }

    protected function onBannerSelectBuilderCreated(Gpf_SqlBuilder_SelectBuilder $builder){
    }
}
?>
