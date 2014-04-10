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
* @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Plugin extends Nostress_Nscexport_Model_Abstract
{   	
	public function _construct() 
	{
		parent::_construct ();
		$this->_init ('nscexport/plugin');
	}
	
	public function updatePluginInfo($info)
	{
		if(empty($info))
			return;
			
		$collection = $this->getCollection()->load();
		
		//update existing items
		foreach ($collection as $item)
		{
			$code = $item->getCode();
			$pluginInfo = array();
			if(isset($info[$code]))
			{
				foreach ($info[$code] as $key => $value)
					$item->setData($key,$value);
				$item->setUpdateTime($this->helper()->getDatetime());
				$item->save();
				
				unset($info[$code]);
			}
		}
		
		//add new items
		$this->insertData($info, $collection); 
	}
	
	protected function insertData($data, $collection) 
	{
		$now = $this->helper()->getDatetime();
		foreach ($data as $itemData) 
		{			
			$colItem = $collection->getNewEmptyItem();
			$colItem->setData($itemData);
			$colItem->setUpdateTime($now);
			$collection->addItem($colItem);
		}
		$collection->save();
	}
}
?>