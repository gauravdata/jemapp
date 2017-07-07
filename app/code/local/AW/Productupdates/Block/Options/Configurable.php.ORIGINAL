<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productupdates
 * @version    2.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Productupdates_Block_Options_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{

    protected $_punProducts = array();

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('productupdates/catalog/product/options/configurable.phtml');
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('productupdates/configurable.js');
 
        return parent::_prepareLayout();
    }
    
    public function getJsonConfig()
    {
        $config = Mage::helper('core')->jsonDecode(parent::getJsonConfig());

        $options = array();
        $options['products'] = array();
        $options['stocks'] = array();
        foreach ($this->getAllowProducts() as $product) {
            $productId = $product->getId();
            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());

                if (!isset($options['products'][$productId])) {
                    $options['products'][$productId] = null;
                }
                if (!isset($options['stocks'][$productId])) {
                    $options['stocks'][$productId] = null;
                }

                $options['products'][$productId] .= $attributeValue;
                $options['stocks'][$productId] = (int) $product->isSalable();
            }
        }

        $config['valueByProduct'] = $options;
        $config['stockMessage'] = array($this->__('Out of stock'), $this->__('In stock'));
        $config['linkMessage'] = array(
            AW_Productupdates_Model_Source_SubscribeLink::STOCK_CHANGE =>
                $this->__('You will be notified by email when this product gets back in stock'),
            AW_Productupdates_Model_Source_SubscribeLink::PRICE_CHANGE =>
                $this->__('You will be notified by email about product updates')
        );
        $config['chooseText'] = $this->__('Please select product configuration');

        return Mage::helper('core')->jsonEncode($config);
    }

    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $allProducts = $this->getProduct()->getTypeInstance(true)->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
                if ($product->getStatus() == 1) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    public function getProduct()
    {
        return $this->getData('product');
    }

}
