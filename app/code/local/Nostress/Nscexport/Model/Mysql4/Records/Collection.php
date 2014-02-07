<?php
/** 
* Magento Module developed by NoStress Commerce 
* 
* NOTICE OF LICENSE 
* 
* This source file is subject to the Open Software License (OSL 3.0) 
* that is bundled with this package in the file LICENSE.txt. 
* It is also available through the world-wide-web at this URL: 
* http://opensource.org/licenses/osl-3.0.php 
* If you did of the license and are unable to 
* obtain it through the world-wide-web, please send an email 
* to info@nostresscommerce.cz so we can send you a copy immediately. 
* 
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* Model for Nscexport
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Mysql4_Records_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/records');
    }
    
	public function joinExportRelationsTable()
	{
		$select = $this->getSelect();
		$mainTable = "main_table";
		$relTable = "relations";
		$select->join(
                    array('relations'=>$this->getResource()->getTable('nscexport/categoryproducts')),
                    $relTable.'.entity_id='.$mainTable.'.relation_id AND '.$relTable.'.export_id = '.$mainTable.'.export_id',
                    array('category_id','product_id'));		
		return $this;
	}
	
	public function addLimit($limit,$offset)
	{
		$select = $this->getSelect();
		$select->limit($limit,$offset);
		return $this;
	}	
	
	public function addWhereCondition($profileId)
	{
		$select = $this->getSelect();
		$select->where('main_table.export_id=?', $profileId);
		return $this;
	}
}