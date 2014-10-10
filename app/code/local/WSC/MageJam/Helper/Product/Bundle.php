<?php

class WSC_MageJam_Helper_Product_Bundle extends Mage_Core_Helper_Abstract
{
	const PRICE_VIEW_PRICE_RANGE = 0;
	
	const PRICE_VIEW_AS_LOW_AS = 1;
	
	const SELECTION_PRICE_TYPE_FIXED = 0;
	
	const SELECTION_PRICE_TYPE_PERCENT = 1;
	
    protected $_product = null;


    /**
     * Returns bundle attributes as array
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getBundleAttributes(Mage_Catalog_Model_Product $currentProduct)
    {
    	$priceType = '';
    	if ($currentProduct->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED){
    		$priceType = 'fixed';
    	}else if ($currentProduct->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC){
    		$priceType = 'dynamic';
    	}
    	$priceView = '';
    	if ($currentProduct->getPriceView() == self::PRICE_VIEW_AS_LOW_AS){
    		$priceView = 'as_low_as';
    	}else if ($currentProduct->getPriceView() == self::PRICE_VIEW_PRICE_RANGE){
    		$priceView = 'price_range';
    	}
        $attributes = array(
            'price_type'  => $priceType,
            'price_view'  => $priceView
        );

        return $attributes;
    }
        
    /**
     * Returns bundle options as array
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getBundleItems(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
        /* @var $typeInstance Mage_Bundle_Model_Product_Type */
        $typeInstance = $product->getTypeInstance(true);
        $typeInstance->setStoreFilter($product->getStoreId(), $product);

        $optionCollection = $typeInstance->getOptionsCollection($product);

        $selectionCollection = $typeInstance->getSelectionsCollection(
            $typeInstance->getOptionsIds($product),
            $product
        );

        $options = $optionCollection->appendSelections(
            $selectionCollection,
            false,
            $this->getSkipSaleableCheck()
        );

        $result = array();
        foreach ($options as $option) {
            $result[] = $this->_convertOptionToArray($option);
        }

        return $result;
    }

    /**
     * Used for compatibility wih old versions, magento 1.6 doesn't have Mage_Catalog_Helper_Product::getSkipSaleableCheck()
     *
     * @return bool
     */
    public function getSkipSaleableCheck()
    {
        /* @var $helper Mage_Catalog_Helper_Product */
        $helper = Mage::helper('catalog/product');
        if(method_exists($helper, 'getSkipSaleableCheck')) {
            return $helper->getSkipSaleableCheck();
        }
        return false;
    }



    /**
     * Converts option to api response format
     *
     * @param $option
     * @return array
     */
    protected function _convertOptionToArray($option)
    {
        $result = array();

        $result['option_id'] = (int)$option->getOptionId();
        $result['required'] = (int)$option->getRequired();
        $result['position'] = (int)$option->getPosition();
        $result['type'] = $option->getType();
        $result['default_title'] = $option->getDefaultTitle();
        $result['title'] = $option->getTitle();
        $result['selections'] = array();

        foreach($option->getSelections() as $selection) {
            $result['selections'][] = $this->_convertSelectionToArray($selection);
        }

        return $result;
    }

    /**
     * Converts selection to api response format
     *
     * @param $selection
     * @return array
     */
    protected function _convertSelectionToArray($selection)
    {
        $result = array();

        $result['product_id'] = (int)$selection->getProductId();
        $result['selection_id'] = (int)$selection->getSelectionId();
        $result['sku'] = $selection->getSku();
        $result['position'] = (int)$selection->getPosition();
        $result['is_default'] = (int)$selection->getIsDefault();
        $result['price'] = $this->_getSelectionPrice($selection);
        $result['name'] = $selection->getName();
        $result['is_saleable'] = (int)$selection->isSaleable();
        $result['qty'] = $selection->getSelectionQty();
        $result['can_change_qty'] = (int)$selection->getData('selection_can_change_qty');

    	$priceType = '';
    	if ($selection->getSelectionPriceType() == self::SELECTION_PRICE_TYPE_FIXED){
    		$priceType = 'fixed';
    	}else if ($selection->getSelectionPriceType() == self::SELECTION_PRICE_TYPE_PERCENT){
    		$priceType = 'percent';
    	}
    	$result['price_type'] = $priceType;
        
        return $result;
    }

    /**
     * Return selection price
     *
     * @param $selection
     * @return string
     */
    protected function _getSelectionPrice($selection)
    {
        return (string) $this->_product->getPriceModel()->getSelectionPreFinalPrice($this->_product, $selection, 1);
    }
}