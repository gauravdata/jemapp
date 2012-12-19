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
class Pap_Merchants_Banner_ComplexListBox extends Gpf_Object implements Gpf_Rpc_TableData {

    const ID = 'id';
    const VALUE = 'value';

    /**
     * @service banner read
     * @return Gpf_Data_RecordSet
     */
    public function getRow(Gpf_Rpc_Params $params) {
        $select = $this->createBannersSelect($params);
        $select->where->add('b.'.Pap_Db_Table_Banners::ID, '=', $params->get(self::SEARCH));
        $select->limit->set(0, 1);
        $recordset = $select->getAllRows();
        foreach ($recordset as $record) {
            $record->set(self::VALUE, $this->_localize($record->get(self::VALUE)));
        }
        return $recordset;
    }

    /**
     * @service banner read
     * @return Gpf_Data_Table
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $data = new Gpf_Data_Table($params);
        $select = $this->createBannersSelect($params);

        $recordset = $select->getAllRows();
        foreach ($recordset as $record) {
            $record->set(self::VALUE, $this->_localize($record->get(self::VALUE)));
        }
        $data->fill($recordset);
        return $data;
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createBannersSelect(Gpf_Rpc_Params $params) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('b.'.Pap_Db_Table_Banners::ID, self::ID);
        $selectBuilder->select->add('b.'.Pap_Db_Table_Banners::NAME, self::VALUE);
        $selectBuilder->from->add(Pap_Db_Table_Banners::getName(), 'b');

        $cond = new Gpf_SqlBuilder_CompoundWhereCondition();
        $cond->add('b.'.Pap_Db_Table_Banners::STATUS, '=', 'A');
        $cond->add('b.'.Pap_Db_Table_Banners::STATUS, '=', 'H','OR');
        $selectBuilder->where->addCondition($cond,'AND');

        if ($params->get('campaignid') != '') {
            $selectBuilder->where->add('b.'.Pap_Db_Table_Banners::CAMPAIGN_ID, '=', $params->get('campaignid'));
        }

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannerListbox.getBannerSelect', $selectBuilder);

        return $selectBuilder;
    }

}

?>
