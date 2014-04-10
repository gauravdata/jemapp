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
 * Model for Export
 * 
 * @category Nostress 
 * @package Nostress_Nscexport
 * 
 */

class Nostress_Nscexport_Model_Categoryproducts extends Mage_Core_Model_Abstract 
{
	const PRODUCTS_IDS = 'product_ids';
	const PRODUCTS_ID = 'product_id';
	const CATEGORY_ID = 'category_id';
	const INDEX_CATEGORY_PRODUCTS = 'category_products';
	const INDEX_CATEGORY_IDS = 'category_ids';
	const PROFILE_ID = "profile_id";
	protected $_store;
	
	public function _construct() 
	{
		parent::_construct ();
		$this->_init ( 'nscexport/categoryproducts' );
	}
	
	public function updateProductAssignment($product)
	{		
		$relationsArray = array();		
		foreach($product->getStoreIds() as $productStoreViewId)
		{
			$this->_store = Mage::app()->getStore($productStoreViewId);
			$profileCollection =  Mage::getModel('nscexport/profile')->getCollectionByStoreId($productStoreViewId);
			$categoryIds = $product->getCategoryIds();

			foreach($profileCollection as $profile)
			{
				foreach($categoryIds as $categoryId)
				{
					if($this->isCategoryInProfile($categoryId,$profile->getId()))
					{
						$relationsArray[] = array(self::CATEGORY_ID => $categoryId,self::PROFILE_ID => $profile->getId());												
						
					}
				}
			}			
		}	
				
		$this->removeProductFromAllProfiles($product->getId());
		$productId = $product->getId();
		$relationsUpdated = false;
		foreach ($relationsArray as $relation) 
		{
			$this->addProductToProfile($productId,$relation[self::CATEGORY_ID],$relation[self::PROFILE_ID]);
			$relationsUpdated = true;
		}
			
		if($relationsUpdated)
			Mage::getSingleton('adminhtml/session')->addSuccess(
                	Mage::helper('nscexport')->__('The export profile-product relations has been updated.')
            	);
	} 
	
	public function updateCategoryProducts($profileId,$categoryproducts,$storeId)
	{
		$this->_store = Mage::app()->getStore($storeId);		
		$data = $this->parseCategoryProducts($categoryproducts);
		$categoryIds = $data[self::INDEX_CATEGORY_IDS];
		$catProdParsed = $data[self::INDEX_CATEGORY_PRODUCTS];
		$this->runQuery($profileId,$catProdParsed,true,$categoryIds);
	}
	
	public function getExportCategoryProducts($profileId)
	{
		$collection = $this->getCategoryProductsCollection($profileId);				
		$collection->getSelect()->columns(array(self::PRODUCTS_IDS => "(GROUP_CONCAT(".self::PRODUCTS_ID." SEPARATOR ','))"));
		$collection->getSelect()->group(self::CATEGORY_ID);
		//$q = $collection->getSelect()->__toString();				
		$data = $collection->getData();
		
		return $this->prepareCategoryProductResult($data);		
	}
	
	public function getExportCategories($profileId)
	{
		$collection = $this->getCategoryProductsCollection($profileId);			
		$select = $collection->getSelect();
		$select->distinct();					
		//$q = $collection->getSelect()->__toString();				
		$data = $collection->getData();		
		
		return $this->prepareCategoriesArray($data);
	}
	
	public function isCategoryInProfile($categoryId,$profileId)
	{
		return $this->getResource()->isCategoryInProfile($categoryId,$profileId);
	}
	
	public function getCategoryProductsCount($profileId)
	{
		return $this->getResource()->getProfileRecordCount($profileId);
	}
	
	public function addProductToProfile($productId,$categoryId,$profileId)
	{
		$categoryproducts = array($categoryId => array($productId));
		$this->runQuery($profileId,$categoryproducts,false);
	}
	
	public function removeProductFromAllProfiles($productId)
	{
		$this->getResource()->deleteRecords(null,$productId);
	}
	
	private function prepareCategoriesArray($data)
	{		
		$result = array();	
		foreach($data as $record)
		{
			$result[] = $record[self::CATEGORY_ID];
		}
		return $result;
	}
	
	private function prepareCategoryProductResult($data)
	{
		$result = "";
		$first = true;
		foreach($data as $record)
		{
			if($first)
				$first = false;
			else
				$result .= "|";
			$result.= $record[self::CATEGORY_ID].",".$record[self::PRODUCTS_IDS];
		}
		unset($data);
		return $result;
	}
	
	protected function getCategoryProductsCollection($profileId)
	{
		$collection = $this->getCollection()->addFieldToFilter('export_id',$profileId);
		$collection->addFieldToSelect(self::CATEGORY_ID);		
		return $collection;
	}
	
	private function runQuery($profileId,$categoryproducts,$deleteRecords,$categoryIds=null)
    {
	    try 
	    {
	    	if($deleteRecords)
            	$this->getResource()->deleteRecords($profileId);
			$this->getResource()->insertRecords($profileId,$categoryproducts);
			$this->getResource()->selectInsertRecords($profileId,$categoryIds,$this->_store->getId());
				
		} 
		catch (Exception $e) 
		{
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage()
                //Mage::helper('catalogrule')->__('Unable to apply rules.')
            );
            throw $e;
	    }
    }  
	
	private function parseCategoryProducts($categoryproducts)
	{
	    $categoryIds = array();
		$result = Array();
		$groups = explode("|",$categoryproducts);
		foreach($groups as $group)
		{
			$members = explode(",",$group);
			if(count($members) == 0 || $members[0] == "" || !is_numeric($members[0]))
				continue;
			$categoryId = $members[0];	
			unset($members[0]);
			$productIds = Array();
			
			$deleted = false;
			foreach($members as $productId)
			{
				if($productId == 'deleted')
				{
					$deleted = true;
					break;
				}
				if(is_numeric($productId))
				{
					$productIds[] = $productId;
				}
			}
			if($deleted)
				continue;
			
			if(count($productIds) == 0)
			{
				$categoryIds[] = $categoryId;
			}
			else
				$result[$categoryId] = $productIds;
		}
		return array(self::INDEX_CATEGORY_PRODUCTS => $result,self::INDEX_CATEGORY_IDS => $categoryIds);
	}
}