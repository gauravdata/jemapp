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
class Pap_Features_BannerRotator_BannersGrid extends Gpf_View_GridService {

    const CTR = "ctr";
    const IMPS = "imp";

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_BannersInRotators::getName());
        $condition = Pap_Db_Table_BannersInRotators::getName().".".Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID." = ".Pap_Db_Table_Banners::getName().".".Pap_Db_Table_Banners::ID;
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Banners::getName(), '', $condition);
    }

    function getBannerId() {
        if ($this->_params->exists('bannerid')) {
            return $this->_params->get('bannerid');
        }
        throw new Gpf_Exception($this->_('Missing banner id'));
    }

    protected function buildWhere() {
        $this->_selectBuilder->where->add(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID, '=', $this->getBannerId());
        parent::buildWhere();
    }

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_Banners::NAME, $this->_("Name"), true);
        $this->addViewColumn(Pap_Db_Table_Banners::SIZE, $this->_("Size"), true);
        $this->addViewColumn(Pap_Db_Table_BannersInRotators::CLICKS, $this->_("Clicks"), true);
        $this->addViewColumn(self::IMPS ,$this->_("Impressions (raw / unique)"));
        $this->addViewColumn(self::CTR, $this->_("CTR (raw / unique)"));
        $this->addViewColumn(Pap_Db_Table_BannersInRotators::VALID_FROM, $this->_("Valid from"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_BannersInRotators::VALID_UNTIL, $this->_("Valid until"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_BannersInRotators::ARCHIVE, $this->_("Archive after expiration"));
        $this->addViewColumn(Pap_Db_Table_BannersInRotators::RANK, $this->_("Rank"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_BannersInRotators::ID);
        $this->addDataColumn(Pap_Db_Table_Banners::NAME);
        $this->addDataColumn(Pap_Db_Table_Banners::SIZE);
        $this->addDataColumn(Pap_Db_Table_BannersInRotators::CLICKS);
        $this->addDataColumn(Pap_Db_Table_BannersInRotators::ALL_IMPS);
        $this->addDataColumn(Pap_Db_Table_BannersInRotators::UNIQ_IMPS);
        $this->addDataColumn(Pap_Db_Table_BannersInRotators::VALID_UNTIL);
        $this->addDataColumn(Pap_Db_Table_BannersInRotators::VALID_FROM);
        $this->addDataColumn(Pap_Db_Table_BannersInRotators::ARCHIVE);
        $this->addDataColumn(Pap_Db_Table_BannersInRotators::RANK);
        $this->addDataColumn(Pap_Db_Table_Banners::TYPE);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_Banners::NAME, '');
        $this->addDefaultViewColumn(Pap_Db_Table_Banners::SIZE, '');
        $this->addDefaultViewColumn(Pap_Db_Table_BannersInRotators::CLICKS, '');
        $this->addDefaultViewColumn(self::IMPS, '');
        $this->addDefaultViewColumn(self::CTR, '');
        $this->addDefaultViewColumn(Pap_Db_Table_BannersInRotators::VALID_UNTIL, '');
        $this->addDefaultViewColumn(Pap_Db_Table_BannersInRotators::VALID_FROM, '');
        $this->addDefaultViewColumn(Pap_Db_Table_BannersInRotators::ARCHIVE, '');
        $this->addDefaultViewColumn(Pap_Db_Table_BannersInRotators::RANK, '');
        $this->addDefaultViewColumn(self::ACTIONS, '');
    }


    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
        $inputResult = parent::afterExecute($inputResult);
        $inputResult->addColumn(self::IMPS);
        $inputResult->addColumn(self::CTR);

        foreach ($inputResult as $record) {
            $clicks =  $record->get(Pap_Db_Table_BannersInRotators::CLICKS);
            $allImp = $record->get(Pap_Db_Table_BannersInRotators::ALL_IMPS);
            $uniqImp = $record->get(Pap_Db_Table_BannersInRotators::UNIQ_IMPS);
            $record->set(self::IMPS, $allImp.' / '.$uniqImp);
            $record->set(self::CTR, '% '.$this->getCTR($clicks,$allImp).' / % '.$this->getCTR($clicks,$uniqImp) );
        }
        return $inputResult;
    }

    function getCTR($clicks,$impression){
        if($impression == 0) {
            return 0;
        } else {
            return round(($clicks / $impression) * 100, 2);
        }
    }

    /**
     * @service banner read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service banner write
     * @return Gpf_Rpc_Serializable
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }
}
?>
