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
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Taxonomy_Setup extends Nostress_Nscexport_Model_Abstract
{   
	const COL_NAME = 'name';
	const COL_CODE = 'code';
	const COL_TYPE = 'type';
	const COL_SETUP = 'setup';
	
	public function _construct() 
	{
		parent::_construct ();
		$this->_init ( 'nscexport/taxonomy_setup' );
	}
	
	public function getTaxonomyAttributeCodes()
	{
	    $collection = $this->getCollection()->load();
	    $codes = array();
	    foreach ($collection as $item) 
	    {
	    	$codes[] = $this->helper()->createCategoryAttributeCode($item->getCode()); 
	    }
	    return $codes;
	}
	
	public function getDecodedSetup()
	{
		$setup = $this->getSetup();
		$setup = $this->helper()->dS($setup);
		$setup = $this->helper()->stringToXml($setup);
		$setup = $this->helper()->XMLnodeToArray($setup);
		return $setup;
	}
	
	public function getSetupByCode($code)
	{
		$collection = $this->getCollection();
		$collection->addFieldToFilter(self::COL_CODE,$code);
		$collection->addFieldToSelect(self::COL_SETUP);
		$collection->getSelect();
		$collection->load();
		foreach($collection as $item)
		{
			return $item->getDecodedSetup();
		}
		return null;
	}
	
	public function getTaxonomyByCode($code)
	{
		$collection = $this->getCollection();
		$collection->addFieldToFilter(self::COL_CODE,$code);
		$collection->getSelect();
		$collection->load();
		foreach($collection as $item)
		{
			return $item;
		}
		return null;
	}
	
	
    public function updateTaxonomies($data)
    {
    	$data = $this->prepareData($data);
    	$this->updateData($data);    	    	
    }
    
    protected function updateData($data)
    {
    	$collection = $this->getCollection()->load();
    	foreach($collection as $item)
    	{
    		$code = $item->getCode();
    		if(isset($data[$code]))
    		{
    			$this->copyData($data[$code],$item);
    			unset($data[$code]);
    		}
    		else 
    		{
    			$item->delete();
    		}
    	}
    	$this->insertData($data,$collection);
    	$collection->save();
    }
    
    protected function insertData($data,$collection)
    {
        foreach($data as $itemData)
    	{
    		$colItem = $collection->getNewEmptyItem();
    		$colItem->setData($itemData);
    		$collection->addItem($colItem);
    	}
    }
    
    protected function copyData($data,$dstItem)
    {
    	foreach($data as $key => $src)
    	{
    		$dstItem->setData($key,$src);
    	}
    }
    
    protected function prepareData($data)
    {
    	$modifData = array();
    	foreach($data as $key => $item)
    	{
    		if(!isset($item[self::COL_CODE]))
    		{	
    			throw new Exception($this->__("Missing taxonomy setup attribute '".self::COL_CODE."'"));
    		}
    		$modifData[$item[self::COL_CODE]] = $item;
    		
    	}
    	return $modifData;
    }		
}
?>