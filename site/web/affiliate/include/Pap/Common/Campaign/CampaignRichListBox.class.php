<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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
class Pap_Common_Campaign_CampaignRichListBox extends Gpf_Ui_RichListBoxService {

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
        $selectBuilder->select->add('c.'.Pap_Db_Table_Campaigns::ID, self::ID);
        $selectBuilder->select->add('c.'.Pap_Db_Table_Campaigns::NAME, self::VALUE);
        $selectBuilder->from->add(Pap_Db_Table_Campaigns::getName(), 'c');
        if(Gpf_Session::getAuthUser()->getRoleTypeId() == Pap_Application::ROLETYPE_AFFILIATE) {
            $selectBuilder->where->add('c.' . Pap_Db_Table_Campaigns::STATUS, '!=', 'S');
        }
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Campaigns.getCampaignsSelect', $selectBuilder);

        return $selectBuilder;
    }
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
        $selectBuilder->where->add('c.'.Pap_Db_Table_Campaigns::NAME, 'LIKE', '%'.$search.'%');
    }

    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
        $selectBuilder->where->add('c.'.Pap_Db_Table_Campaigns::ID, '=', $id);
    }
}

?>
