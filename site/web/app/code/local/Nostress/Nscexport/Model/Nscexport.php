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

class Nostress_Nscexport_Model_Nscexport extends Mage_Core_Model_Abstract {
	private $product; //current processed product
	private $store; //current chosen store
	private $category; //category for which i product exported
	private $_taxHelper; //tax helper
	private $encoding; //chosen encoding
	private $editedCategoryIds; //edited ids of categories	
	private $configManageStock = ''; //edited ids of categories
	

	public function _construct() 
	{
		Mage::helper('nscexport/version')->validateLicenceBackend();
		parent::_construct ();
		$this->_init ( 'nscexport/nscexport' );
		$this->_taxHelper = new Mage_Tax_Helper_Data ( );
	}
	
	public function getCollectionByStoreId($storeId)
	{
		$collection = $this->getCollection()->addFieldToFilter('store_id',$storeId);
		$collection->load();
		return $collection;
	}
	
	public function setMessageAndStatus($message,$status)
	{		
    	$this->setMessage($message);
    	$this->setStatus($status);
    	$this->save();
	}
	
	public function setNewProductId()
	{
		$offset = (int)$this->getProductId();
		$count = (int)Mage::getConfig()->getNode('default/nscexport/nscexport/products_to_nscexport');
		$this->setProductId($offset+$count);
        $this->save();
	}
	
	/*
	 * Returns product final price includeing tax
	 */
	public function getProductPriceInclTax($format = false) 
	{
		return $this->convertProductPrice(true,false,$format);
	}
	
	/*
	 * Returns product final price excludeing tax
	 */
	public function getProductPriceExclTax($format = false) 
	{
		return $this->convertProductPrice(false,false,$format);
	}
	
	/*
	* Returns product original price includeing tax
	*/
	public function getProductOriginalPriceInclTax($format = false) 
	{
		return $this->convertProductPrice(true,true,$format);
	}
	
	/*
	* Returns product original price excludeing tax
	*/
	public function getProductOriginalPriceExclTax($format = false) 
	{
		return $this->convertProductPrice(false,true,$format);
	}
		
	/**
	 * Returns price alng the parametrs settings
	 *
	 * @param bool $includeTax
	 * @param bool $original
	 * @return int
	 */
	protected function convertProductPrice($includeTax,$original,$format)
	{
		if($original)
			$price = $this->product->getPrice ();
		else 
			$price = $this->product->getFinalPrice ();			

		if ($this->product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE || $this->product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) 
		{		
			$price = $this->product->getMinimalPrice ();	
		}
		
		$price = $this->store->convertPrice($price);						
		$price = $this->_taxHelper->getPrice ( $this->product, $price, $includeTax);		
		
		
		if($format)
		{
			$price = $this->formatPrice($price);
		}
		else 
		{
			$price = sprintf("%01.2f", $price);		
			//replace decimal dot with comma
			if(Mage::getConfig()->getNode('default/nscexport/engine/'.strtolower($this->getSearchengine()).'/delimiter') == ',')
			{
				$price = str_replace(".",",",$price);
			}
		}
		return $price;
	}
	
	public function isProductType($cmpType)
	{
		$productType = $this->getProductType();
		$type = Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
		switch($cmpType)
		{
			case 'bundle':
				$type = Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
				break;
			case 'gruped':
				$type = Mage_Catalog_Model_Product_Type::TYPE_GROUPED;
				break;
			default:
				break;
		}
		
		return $type == $productType;
		
	}
	
	public function formatPrice($price,$includeContainer = false)
	{
		return $this->store->formatPrice($price,$includeContainer);
	}
	
	public function setStore($store) {
		$this->store = $store;
		if (strpos ( $this->getCategoryIds (), ',' ) === 0)
			$this->setCategoryIds ( substr ( $this->getCategoryIds (), 1 ) );
		$this->save ();
		$this->editedCategoryIds = str_replace ( ',', '|', $this->getCategoryIds () );
	}
	
	public function getStore() 
	{
		return $this->store;
	}
	
	public function getEditedCategoryIds() {
		return $this->editedCategoryIds;
	}
	
	public function setNscexportEncoding($encoding) {
		$this->encoding = $encoding;
	}
	
	public function getNscexportEncoding() {
		return $this->encoding;
	}
	
	public function setProduct($product) {
		$this->product = $product;
	}
	
	public function setCategory($category) {
		$this->category = $category;
	}
	
	/**
	 * Returns product name
	 *
	 * @return unknown
	 */
	public function getProductName() {
		if ($this->product->getMetaTitle () == '')
			return $this->product->getName ();
		else
			return $this->product->getMetaTitle ();
	}
	
	/**
	 * Returns products short description
	 *
	 * @return unknown
	 */
	public function getProductDescription($removeEndOfLine = false) {
		if ($this->product->getMetaDescription () == '')
			$result = $this->product->_getData ( 'short_description' );
		else
			$result = $this->product->getMetaDescription ();
		
		if($removeEndOfLine)
		{
			$carriage= array("\r\n", "\r", "\n");			
			$result = str_replace($carriage, "", $result);
		}
		return $result;
	}
	
	/**
	 * Returns product long description
	 *
	 * @return unknown
	 */
	public function getProductLongDescription($removeEndOfLine = false) 
	{
		$result = $this->product->_getData ( 'description' );
		if($removeEndOfLine)
		{
			$carriage= array("\r\n", "\r", "\n");			
			$result = str_replace($carriage, "", $result);
		}//	$result = str_replace("\r\n"," ",str_replace("\n"," ",$result));
		return $result;
	}
	
	/**
	 * Returns number of contributions for current product and store view.
	 */
	public function getProductNumberOfContributions() {
		return $this->getReviewsCollection ()->getSize ();
	}
	
	/*
	 * Returns collection of reviews for current product and store
	 */
	private function getReviewsCollection() {
		return Mage::getModel ( 'review/review' )->getCollection ()->addStoreFilter ( $this->getStoreId () )->addStatusFilter ( 'approved' )->addEntityFilter ( 'product', $this->product->getId () )->setDateOrder ();
	}
	
	/*
	 * Returns reviews url
	 */
	public function getProductReviewsUrl() {
		return $this->store->getBaseUrl ( 'link', false ) . 'review/product/list/id/' . $this->product->getId ();
	}
	
	/**
	 * Returns product url address
	 *
	 * @return unknown
	 */
	public function getProductUrl() 
	{	
		Mage::unregister('current_category');
		if(Mage::helper('nscexport')->getNscGeneralStoreConfig(Nostress_Nscexport_Helper_Data::XML_PATH_NSC_GENERAL_URL_CATEGORY) == 1)
			Mage::register('current_category',$this->category);
		
		$productUrl = $this->product->getProductUrl(true);
			
		if(Mage::helper('nscexport')->getNscGeneralStoreConfig(Nostress_Nscexport_Helper_Data::XML_PATH_NSC_GENERAL_URL_STORE) == 1)
			return $productUrl;
		
		$current_url = explode('?', $productUrl);
		return $current_url[0];
	}
	
	/**
	 * Returns tru if product is held as new
	 *
	 * @return unknown
	 */
	public function getProductIsNew() 
	{
		if ($this->product->getNewsFromDate () <= now () && now () <= $this->product->getNewsToDate ())
			return true;
		else
			return false;
	}
	
	/**
	 * Returns true if product is in stock
	 *
	 * @return unknown
	 */
	public function getProductIsInStock() {
		$stockItem = Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $this->product );

		if($stockItem->getUseConfigManageStock())
		{
			if($this->getConfigManageStock() == '0')
				return false;
		}
		else
		{
			if(!$stockItem->getManageStock())
				return false;
		}
			
		return $stockItem->getIsInStock ();
	}
	
	/*
	 * Returns url path to products image.
	 */
	public function getProductImageUrl() {
		$baseUrl = $this->store->getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA, false );
		if(strpos(strtolower($baseUrl),"fugu") !== false)
		{
			return $baseUrl. 'alter/' . str_replace('swf', 'jpg',$this->product->getData ('flash'));
			//$this->getUrl('media/alter/').str_replace('swf', 'jpg', $atr->getFrontend()->getValue($product));
		}
		else 
		{
			$imageUrlSuffix = $this->product->getImage();
			if($imageUrlSuffix == '')
			{
				$imageUrlSuffix = '/placeholder/'.$this->store->getWebsite()->getConfig('catalog/placeholder/image_placeholder');
			}
			return $baseUrl. 'catalog/product' . $imageUrlSuffix;
		}		 
	}
	
	/**
	 * Returns product manufacturer-brand
	 *
	 * @return unknown
	 */
	public function getProductManufacturer() 
	{
		$r = "";
		if($this->product->hasData('manufacturer'))
			$r =  $this->product->getAttributeText('manufacturer');
		return $r;
	}
		
	/**
	 * Returns percent of tax
	 *
	 * @return unknown
	 */
	public function getProductTaxPercent() 
	{
		return $this->product->getTaxPercent ();
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function getProductCategoryName() 
	{
		return $this->category->getName ();
	}
	
	public function getProductCategoryId() 
	{
		return $this->category->getId ();
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function getProductParentCategoryName() 
	{
		$parentCategory = $this->category->getParentCategory();
		if($parentCategory->getId() == $this->store->getRootCategoryId ())
			return '';
		return $parentCategory->getName ();
	}
	
	/**
	 * Returns category tree
	 *
	 * @param unknown_type $level
	 * @param unknown_type $subcategoryTag
	 * @return unknown
	 */
	public function getProductSubcategoriesXmlTree($level, $subcategoryTag)
	{	
		return $this->addSubcategories($this->category,$level, $subcategoryTag);
	}
	
	public function getStoreSubcategoriesXmlTree($level, $subcategoryTag)
	{			
		$category = Mage::getModel('catalog/category')->load($this->store->getRootCategoryId());
		$result = '<'.$subcategoryTag.' name="'.Nostress_Nscexport_Helper_Data::formatContent($category->getName()).'" id="'.$category->getId().'" >';	
		$result .= $this->addSubcategories($category,$level,  $subcategoryTag);
		$result .= '</'.$subcategoryTag.'>';
		return $result;
	}
	
	private function addSubcategories($category,$level, $subcategoryTag)
	{
		if(!$category->hasChildren())
			return '';
	    $children = $category->getChildrenCategories();
		$result = '';
		$level = $level - 1;
		
		foreach($children as $child)
		{
			$result .= '<'.$subcategoryTag.' name="'.Nostress_Nscexport_Helper_Data::formatContent($child->getName()).'" id="'.$child->getId().'" >';	
			if($level > 0)
				$result .= $this->addSubcategories($child,$level, $subcategoryTag);
			$result .= '</'.$subcategoryTag.'>';
		}
		return $result;
	}
	
	/**
	 * Returns full category path
	 *
	 * @param string $delimiter
	 * @return unknown
	 */
	public function getProductFullCategoryPath($delimiter) 
	{				
		$rootCatId = $this->store->getRootCategoryId ();
		$category = $this->category;
		$pathInStore = $category->getPathInStore();
        $pathIds = array_reverse(explode(',', $pathInStore));

        //remove catalog root category id
		unset($pathIds[array_search($rootCatId,$pathIds)]);
        $categories = $category->getParentCategories();
		$result = '';//$category->getName ();
        
		// add category path to result
        foreach ($pathIds as $categoryId) 
        {
        	if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) 
        	{
        		$categories[$categoryId]->setStoreId($this->getStoreId ())->load('name');
        		$result .= $delimiter.$categories[$categoryId]->getName();
            }
        }
        $result = trim($result,$delimiter);
		return $result;
	}
	
	/**
	 * Return product id
	 *
	 * @return unknown
	 */
	public function getProductCurrentId() 
	{
		return $this->product->getId ();
	}
	
	/**
	 * Returns product sku
	 *
	 * @return unknown
	 */
	public function getProductSku() 
	{
		return $this->product->getSku ();
	}
	
	/**
	 * Returns product type
	 *
	 * @return unknown
	 */
	public function getProductType() 
	{
		return $this->product->getTypeId();
	}
	
	/**
	 * Returns true if product has data with name $name
	 *
	 * @param unknown_type $name
	 * @return unknown
	 */
	public function productHasData($name) 
	{
		return $this->product->hasData ( $name );
	}
	
	/**
	 * Check if is possible to use current $index
	 *
	 * @param unknown_type $index
	 * @return True/False
	 */
	public function hasProductImageUrlNumber($index)
	{
		$product = Mage::getModel('catalog/product')->load($this->product->getId());
		//$this->product->getMediaAttributes();
		//$product->getMediaAttributes();
		$collection = $product->getMediaGalleryImages();
		//$f = fopen("testArr.txt","w");
		
		//foreach($collection as $image)
		//	fwrite($f,$image->getUrl());
		//fclose($f);
		return count($collection) > $index;
		return count($this->product->getMediaGalleryImages()) >= $index;
	}
	
	/**
	 * Returns product image with index $index from image collection.
	 *
	 * @return ImageUrl
	 */
	public function getProductImageUrlNumber($index)
	{
		//$product = Mage::getModel('catalog/product')->load($this->product->getId());
		//$collection = $product->getMediaGalleryImages()->toArray();
		//return $collection[$index][$url];
		/*$resultArr = array(); 
		$i = 0;
		foreach($collection as $image)
		{
			$resultArr[$i] = $image->getUrl();
			$i = $i +1;
		}
		return $resultArr;*/
	}
	
	/**
	 * Returns product image url collection.
	 *
	 * @return ImageUrl collection
	 */
	public function getProductImageUrlArray()
	{
		$product = Mage::getModel('catalog/product')->load($this->product->getId());		
		$collection =  $product->getMediaGalleryImages();
		$resultArr = array(); 
		$i = 0;	
		foreach($collection->getItems()  as $image)
		{
			$resultArr[$i] = $image->getUrl();//->getUrl();
			$i = $i +1;
		}
		return $resultArr;
	}
		
	/**
	 * Returns value of optional attribute
	 *
	 * @param String $attributeCode  = attribute name
	 * @return value of attribute
	 */
	public function getProductOptionalAtribute($attributeCode) 
	{
		$r =  $this->product->getAttributeText($attributeCode);
		return $r;
	}
	
	/**
	 * Returns value of text attribute
	 *
	 * @param String $attributeCode  = attribute name
	 * @return value of attribute
	 */
	public function getProductTextAtribute($attributeCode) {
		return $this->product->getData ( $attributeCode );
	}
	
	/**
	 * Returns product currency
	 *
	 * @return unknown
	 */
	public function getProductCurrency() 
	{
		return $this->store->getWebsite ()->getConfig ( Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE );
	}
	
	/**
	 * Returns product weight
	 *
	 * @return unknown
	 */
	public function getProductWeight() {
		return $this->product->getWeight ();
	}
	
	/**
	 * Returns product costs
	 *
	 * @return unknown
	 */
	public function getProductShippingCost() {
		return $this->product->getCost ();
	}
	
	/**
	 * nepouzivat
	 *
	 * @return unknown
	 */
	public function getProductShippingCostExclTax() {
		$price = $this->product->getCost ();
		return $this->_taxHelper->getPrice ( $this->product, $price, false );
	}
	
	/**
	 * nepouzivat
	 *
	 * @return unknown
	 */
	public function getProductShippingCostInclTax() {
		$price = $this->product->getCost ();
		return $this->_taxHelper->getPrice ( $this->product, $price, true );
	}
	
	public function productHasSpecialPrice() 
	{ 	// prices the same
		if($this->product->getSpecialPrice()==0)
			return false;
		// Maybe, if date mathes
		// Must have begin
		if(now() <= $this->product->getSpecialFromDate())
		{	// not yet
			return false;
		}

		// if end is set,
		if($this->product->getSpecialToDate() != 0)
		{
			// ok if not reached
			return (now() <= $this->product->getSpecialToDate());
		}

		// so have begin and not reached...
		return true;
		
		
		/*
		if ($this->product->getSpecialFromDate () <= now () && $this->product->getSpecialToDate () >= now ())
			return true;
		else
			return false;*/
	}
	
	/**
	 * Returns special price of product including tax
	 *
	 * @return unknown
	 */
	public function getProductSpecialPriceInclTax() 
	{
		$price = $this->store->convertPrice($this->product->getSpecialPrice ());
		return $this->_taxHelper->getPrice ( $this->product, $price, true );
	}
	
	/**
	 * Returns special price of product including tax
	 *
	 * @return unknown
	 */
	public function getProductSpecialPriceExclTax() 
	{
		$price = $this->store->convertPrice($this->product->getSpecialPrice ());
		return $this->_taxHelper->getPrice ( $this->product, $price, false );
	}
	
	/**
	 * Returns quantity of products
	 *
	 * @return unknown
	 */
	public function  getProductQuantity()
	{
		$stockItem = Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $this->product );
        if($stockItem->getFieldValue('is_qty_decimal') == 1)
			return $stockItem->getQty();
		else
			return (int)$stockItem->getQty();
	}
	
	/**
	 * Returns default stores language.
	 */
	public function getStoreLanguage()
	{
		return substr($this->store->getConfig('general/locale/code'),0,2);
	}
	
	/**
	 * Returns default stores country.
	 */
	public function getStoreCountry()
	{
		return $this->store->getConfig('general/country/default');
	}
	
	private function getConfigManageStock()
	{
		if($this->configManageStock == '')
		{
			$this->configManageStock = (string)Mage::getConfig()->getNode('default/cataloginventory/item_options/manage_stock');
		}
		return $this->configManageStock;	
	}
}