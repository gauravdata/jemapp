<?php

class AW_Colorswatches_Helper_Product
{
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param string|null $containerId
     *
     * @return string
     */
    public static function getSPConfigAsJSON(Mage_Catalog_Model_Product $product, $containerId = null)
    {
        $_configurableTypeBlock = Mage::app()->getLayout()->createBlock("catalog/product_view_type_configurable")
            ->setProduct($product)
        ;
        $config = Mage::helper('core')->jsonDecode($_configurableTypeBlock->getJsonConfig());
        if (null !== $containerId) {
            $config['containerId'] = $containerId;
        }
        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public static function getOptionsPriceConfigAsJSON(Mage_Catalog_Model_Product $product)
    {
        $originProduct = Mage::registry('product');
        Mage::unregister('product');
        Mage::register('product', $product);
        $_catalogProductViewBlock = Mage::app()->getLayout()->createBlock("catalog/product_view");
        $result = $_catalogProductViewBlock->getJsonConfig();
        if (null !== $originProduct) {
            Mage::unregister('product');
            Mage::register('product', $originProduct);
        }
        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public static function getAddToCartUrl(Mage_Catalog_Model_Product $product)
    {
        return Mage::helper('checkout/cart')->getAddUrl($product);
    }
}