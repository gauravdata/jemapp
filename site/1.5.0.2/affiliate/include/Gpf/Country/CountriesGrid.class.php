<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CurrenciesGrid.class.php 20370 2008-08-29 09:11:06Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Country_CountriesGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_Countries::COUNTRY, $this->_("Country"), true);
        $this->addViewColumn(Gpf_Db_Table_Countries::STATUS, $this->_("Is allowed"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }
    
    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Countries::ID);
        $this->addDataColumn(Gpf_Db_Table_Countries::COUNTRY_CODE);
        $this->addDataColumn(Gpf_Db_Table_Countries::COUNTRY);
        $this->addDataColumn(Gpf_Db_Table_Countries::STATUS);
    }
    
    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_Countries::COUNTRY, '100px', 'A');
        $this->addDefaultViewColumn(Gpf_Db_Table_Countries::STATUS, '20px');
        $this->addDefaultViewColumn(self::ACTIONS, '20px');
    }
    
    protected function buildFrom() {
    	$this->_selectBuilder->from->add(Gpf_Db_Table_Countries::getName());
    }
    
    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
    	$inputResult->addColumn('default', Gpf::NO);
    	$defaultCountry = Gpf_Settings::get(Gpf_Settings_Gpf::DEFAULT_COUNTRY);
    	foreach ($inputResult as $country) {
    		if ($country->get(Gpf_Db_Table_Countries::COUNTRY_CODE) == $defaultCountry) {
    			$country->set('country', $this->_localize($country->get('country')) . ' ' . $this->_('(default)'));
    			$country->set('default', Gpf::YES);
    			continue;
    		}
    		$country->set('country', $this->_localize($country->get('country')));
    	}
        return $inputResult;
    }
    
    /**
     * @service country read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
}
?>
