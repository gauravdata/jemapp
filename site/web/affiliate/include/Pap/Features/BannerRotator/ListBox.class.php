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
class Pap_Features_BannerRotator_ListBox extends Pap_Merchants_Banner_ListBox {

    protected function onBannerSelectBuilderCreated(Gpf_SqlBuilder_SelectBuilder $builder){
        $builder->select->add('b.'.Pap_Db_Table_Banners::SIZE, 'size');
        $builder->select->add('c.'.Pap_Db_Table_Campaigns::NAME, 'campaignName');
        $builder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c', 
            'b.'.Pap_Db_Table_Banners::CAMPAIGN_ID.'=c.'.Pap_Db_Table_Campaigns::ID);
        $builder->where->add('b.'.Pap_Db_Table_Banners::TYPE , '!=' , 'R');
    }
    
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
    	$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
    	$condition->add('b.'.Pap_Db_Table_Banners::NAME, 'LIKE', '%'.$search.'%', 'OR');
        $condition->add('b.'.Pap_Db_Table_Banners::SIZE, 'LIKE', '%'.$search.'%', 'OR');
        $condition->add('c.'.Pap_Db_Table_Campaigns::NAME, 'LIKE', '%'.$search.'%', 'OR');
        $selectBuilder->where->addCondition($condition);
    }    
}
?>
