<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
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
class Pap_Features_PapGeoip_CountrySelectGrid extends Gpf_View_GridService {
	
    protected function initViewColumns() {    
        $this->addViewColumn('name', $this->_('Name'), true);        
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn('co.'.Gpf_Db_Table_Countries::COUNTRY_CODE);
        $this->addDataColumn('name', 'co.' . Gpf_Db_Table_Countries::COUNTRY_CODE);               
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn('name', '');
    }
    
    protected function buildFrom(){
    	$this->_selectBuilder->from->add(Gpf_Db_Table_Countries::getName(), 'co');    	
    }
    
    protected function buildWhere() {
        $this->_selectBuilder->where->add(Gpf_Db_Table_Countries::STATUS, '=', Gpf_Db_Country::STATUS_ENABLED);   	
    }
    
    protected function buildOrder() {
    	$this->_selectBuilder->orderBy->add(Gpf_Db_Table_Countries::ORDER);
        $this->_selectBuilder->orderBy->add(Gpf_Db_Table_Countries::COUNTRY);
    }

  /**
     * @service country read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $params->add('limit', 300);
        return parent::getRows($params);
    }    
}
?>
