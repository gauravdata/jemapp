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
* Product loader for export process
* @category Nostress
* @package Nostress_Nscexport
*
*/

class Nostress_Nscexport_Model_Data_Loader_Product extends Nostress_Nscexport_Model_Data_Loader
{
    const PRODUCTS_FILTER = 0;
    const PRODUCTS_ALL = 1;
    const PRODUCTS_FILTER_BY_CATEGORY = 2;
    const PRODUCTS_ALL_BY_CATEGORY = 3;
      
    public function _construct()
    {
        // Note that the export_id refers to the key field in your database table.
        $this->_init('nscexport/data_loader_product', 'entity_id');
    }

    public function initAdapter()
    {
        parent::initAdapter();
        
        $this->reloadProfileDependentCache();
        if($this->getReloadCache())
        	$this->reloadCache();
        $this->basePart();
        $this->commonPart();
        //echo $this->adapter->getSelect()->__toString();
        //exit();
    }
    
    protected function reloadProfileDependentCache()
    {    	
    	$categories = Mage::getModel('nscexport/cache_categories');
    	$categories->setLowestLevel($this->getCategoryLowestLevel());
    	$categories->reload($this->getStoreId());
    }
    
    protected function reloadCache()
    {    	
    	$storeId = $this->getStoreId();
    	$websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
    	$this->helper()->reloadCache(array($storeId),array($websiteId));       
    }
    
    //***************************BASE PART**************************************
    protected function basePart()
    {
    	$filterByProducts = $this->getUseProductFilter();
    	$groupByCategory = $this->getGroupByCategory();
    	if($filterByProducts && $groupByCategory)
    	{
    		$this->productsFilterByCategory();
    	}
        else if(!$filterByProducts && $groupByCategory)
    	{
    		$this->productsAllByCategory();
    	}
        else if(!$filterByProducts && !$groupByCategory)
    	{
    		$this->productsAll();
    	}
    	else
    	{	
    		$this->productsFilter();
    	}
    }
    
    /**
     * Init sql.
	 * Load filteres products from current store.
     */
	protected function productsFilter()
	{
		$this->adapter->joinProductRelation();
		$this->adapter->joinProductFilter ();
		if ($this->loadAllProductCategoriesCondition())
		{
			//add all category information
			$this->adapter->joinAllCategoriesCache ();
		}

		//if process should export also child products of selected parent products 
		$this->adapter->joinCategoryFlatWithFilteredProducts();
		$this->adapter->joinExportCategoryProductMaxLevel ();		
		
		$this->adapter->joinTaxonomy();
		$this->adapter->joinParentCategory();
		
		$this->adapter->groupByProduct ();
	}
	
	/**
	 * Init sql.
	 * Load all products from current store.
	 */
	protected function productsAll()
	{
		if ($this->loadAllProductCategoriesCondition())
		{
			//add all category information
			$this->adapter->joinAllCategoriesCache ();
		}
		
		//add category information
		$this->adapter->joinCategoryProduct ();
		$this->adapter->joinCategoryFlat();
		$this->adapter->joinProductCategoryMaxLevel ();
		$this->adapter->joinTaxonomy();
		$this->adapter->joinParentCategory();
		$this->adapter->joinProductRelation();
		$this->adapter->groupByProduct();
	}
	
	/**
	 * Init sql.
	 * Load all products from current store, order by category.
	 */
	protected function productsAllByCategory()
	{
		$this->adapter->joinCategoryProduct ();
		$this->adapter->joinCategoryFlat ();
		
		if (!$this->loadAllProductCategoriesCondition())
		{
			//one category per product
			$this->adapter->joinProductCategoryMaxLevel ();
			$this->adapter->groupByProduct ();
		}
		$this->adapter->orderByCategory ();
		$this->adapter->joinTaxonomy();
		$this->adapter->joinParentCategory();
		$this->adapter->joinProductRelation();
	}
	
	/**
	 * Init sql.
	 * Load filtered products from current store, order by category.
	 */
	protected function productsFilterByCategory()
	{
		$this->adapter->joinProductFilter ();
		//add category information
		$this->adapter->joinCategoryFlat ();
		
		if(!$this->loadAllProductCategoriesCondition())
		{
			//one category per product
			$this->adapter->joinExportCategoryProductMaxLevel ();
			$this->adapter->groupByProduct ();
		}
		$this->adapter->orderByCategory ();
		$this->adapter->joinTaxonomy();
		$this->adapter->joinParentCategory();
		$this->adapter->joinProductRelation();
	}
	
	protected function loadAllProductCategoriesCondition()
    {
    	$allProductCategories = $this->getLoadAllProductCategories();
    	return $allProductCategories;
    }
    
    //***************************COMMON PART**************************************
    
    protected function commonPart()
    {
    	$this->adapter->joinProductEntity();
    	$this->adapter->joinProductUrlRewrite(); 
    	$this->adapter->joinCategoryUrlRewrite();
    	$this->adapter->addTypeCondition();
    	$this->visibility();
    	
    	$this->adapter->addSortAttribute();
    	$this->adapter->setProductsOrder();
    	
    	$this->stock();
        $this->adapter->joinSuperAttributesCache();
        $this->adapter->joinMediaGalleryCache();
        $this->adapter->joinReview();
        $this->price();
        
        $this->adapter->addAttributeFilter();
        $this->adapter->joinWeee();
        $this->adapter->joinCustomTables();
    }

    protected function visibility()
    {
    	$this->adapter->addVisibilityCondition();
    }
    
    protected function stock()
    {
    	$this->adapter->joinStock();
    	$this->adapter->addStockCondition();
    }
    
    protected function price()
    {
    	$this->adapter->joinTax();
    	$this->adapter->joinPrice();
    }
}
?>