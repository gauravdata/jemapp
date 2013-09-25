<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
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
 * @package GwtPhpFramework
 */
class Gpf_Country_EuCountryData extends Gpf_Country_CountryData implements Gpf_Rpc_TableData {	
	/**
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	protected function createCountriesSelect() {
		$select = parent::createCountriesSelect();		
		$select->where->add(Gpf_Db_Table_Countries::COUNTRY_CODE, 'IN', array(
		'AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI','ES','SE','GB'
		));
		return $select;
	}		
}
?>
