<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Common_Banner_DestinationUrlRichListBox extends Gpf_Ui_RichListBoxService {

   /**
     * @service campaign read
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
        $selectBuilder->select->add('DISTINCT('.'b.'.Pap_Db_Table_Banners::DESTINATION_URL.')', self::ID);
        $selectBuilder->select->add('b.'.Pap_Db_Table_Banners::DESTINATION_URL, self::VALUE);
        $selectBuilder->from->add(Pap_Db_Table_Banners::getName(), 'b');
        $selectBuilder->where->add('b.'.Pap_Db_Table_Banners::DESTINATION_URL, '<>', 'NULL');
        $selectBuilder->where->add('b.'.Pap_Db_Table_Banners::DESTINATION_URL, '<>', '');
        if (Gpf_Session::getAuthUser()->getRoleTypeId() == Pap_Application::ROLETYPE_AFFILIATE) {
            $selectBuilder->where->add('b.'.Pap_Db_Table_Banners::STATUS, '<>', Pap_Db_Banner::STATUS_HIDDEN);
        }
        $selectBuilder->orderBy->add(Pap_Db_Table_Banners::DESTINATION_URL);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannerListbox.getBannerSelect', $selectBuilder);    
        return $selectBuilder;
    }
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
        $selectBuilder->where->add(Pap_Db_Table_Banners::DESTINATION_URL, 'LIKE', '%'.$search.'%');
    }
    
    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
        $selectBuilder->where->add(Pap_Db_Table_Banners::DESTINATION_URL, '=', $id);
    }
}

?>
