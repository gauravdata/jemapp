<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Merchants_User_DirectLinksGrid extends Pap_Common_User_DirectLinksGridBase {

    function __construct() {
        parent::__construct();
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::USER_ID, '', 'N');
    	$this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::URL, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::STATUS, '', 'N');
        $this->addDefaultViewColumn('channel', '', 'N');
        $this->addDefaultViewColumn('banner', '', 'N');
        $this->addDefaultViewColumn('campaign', '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_DirectLinkUrls::NOTE, '', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
    }    
    
    protected function initDataColumns() {
    	parent::initDataColumns();
    }
    
    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
            default:
                break;
        }
    }
    
    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('au.username', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.firstname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('au.lastname', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add(Pap_Db_Table_DirectLinkUrls::URL, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('b.name', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('c.name', 'LIKE', '%'.$filter->getValue().'%', 'OR');
        
        $this->_selectBuilder->where->addCondition($condition);
    }
    
    public function buildWhere() {
        parent::buildWhere();
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix', 'operator'), array('b', 'OR'))));
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix', 'operator'), array('c', 'OR'))));
    }
    
    /**
     * @service direct_link read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service direct_link export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
