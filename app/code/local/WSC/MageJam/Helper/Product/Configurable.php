<?php

class WSC_MageJam_Helper_Product_Configurable extends Mage_Core_Helper_Abstract
{
    /**
     * Get allowed attributes
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getAllowAttributes(Mage_Catalog_Model_Product $product)
    {
        return $product->getTypeInstance(true)->getConfigurableAttributes($product);
    }

    /**
     * Check if allowed attributes have options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasOptions(Mage_Catalog_Model_Product $product)
    {
        $attributes = $this->getAllowAttributes($product);
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
                if ($attribute->getData('prices')) {
                    return true;
                }
            }
        }
        return false;
    }

    protected $allowProducts = null;

    /**
     * Get Allowed Products
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getAllowProducts(Mage_Catalog_Model_Product $product)
    {
        $products = array();
        $skipSaleableCheck = $this->getSkipSaleableCheck();
        $allProducts = $product->getTypeInstance(true)
            ->getUsedProducts(null, $product);
        foreach ($allProducts as $product) {
            if ($product->isSaleable() || $skipSaleableCheck) {
                $products[] = $product;
            }
        }
        return $products;
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
     * retrieve current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Composes configuration for js
     *
     * @param Mage_Catalog_Model_Product $currentProduct
     * @return array
     */
    public function getConfigurableAttributes(Mage_Catalog_Model_Product $currentProduct)
    {
        $attributes = array();
        $options    = array();

        foreach ($this->getAllowProducts($currentProduct) as $product) {
            $productId  = $product->getId();

            foreach ($this->getAllowAttributes($currentProduct) as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }

                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = array('id' => $productId, 'sku' => $product->getSku());
            }
        }

        foreach ($this->getAllowAttributes($currentProduct) as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
                'id'        => $productAttribute->getId(),
                'code'      => $productAttribute->getAttributeCode(),
                'label'     => $attribute->getLabel(),
                'options'   => array()
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
                    $currentProduct->setConfigurablePrice(
                        $this->_preparePrice($currentProduct, $value['pricing_value'], $value['is_percent'])
                    );
                    $currentProduct->setParentId(true);
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        array('product' => $currentProduct)
                    );
                    $configurablePrice = $currentProduct->getConfigurablePrice();

                    if (isset($options[$attributeId][$value['value_index']])) {
                        $productsIndex = $options[$attributeId][$value['value_index']];
                    } else {
                        $productsIndex = array();
                    }

                    $info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_prepareOldPrice($currentProduct, $value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
                    );
                    $optionPrices[] = $configurablePrice;
                }
            }
            if($this->_validateAttributeInfo($info)) {
                $attributes[] = $info;
            }
        }
        return $attributes;
    }

    /**
     * Validating of super product option value
     *
     * @param array $attributeId
     * @param array $value
     * @param array $options
     * @return boolean
     */
    protected function _validateAttributeValue($attributeId, &$value, &$options)
    {
        if(isset($options[$attributeId][$value['value_index']])) {
            return true;
        }

        return false;
    }

    /**
     * Validation of super product option
     *
     * @param array $info
     * @return boolean
     */
    protected function _validateAttributeInfo(&$info)
    {
        if(count($info['options']) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Calculation real price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * @param bool $isPercent
     * @return mixed
     */
    protected function _preparePrice(Mage_Catalog_Model_Product $product, $price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $product->getFinalPrice() * $price / 100;
        }

        return $this->_registerJsPrice($this->_convertPrice($price, true));
    }

    /**
     * Calculation price before special price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * @param bool $isPercent
     * @return mixed
     */
    protected function _prepareOldPrice(Mage_Catalog_Model_Product $product, $price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $product->getPrice() * $price / 100;
        }

        return $this->_registerJsPrice($this->_convertPrice($price, true));
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @param boolean $round
     * @return float
     */
    protected function _convertPrice($price, $round = false)
    {
        if (empty($price)) {
            return 0;
        }

        $price = $this->getCurrentStore()->convertPrice($price);
        if ($round) {
            $price = $this->getCurrentStore()->roundPrice($price);
        }

        return $price;
    }
}