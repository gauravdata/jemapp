<?php

class AW_Colorswatches_Helper_ProductInfoGetter
{
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public static function title($product)
    {
        /** @var Mage_Catalog_Helper_Output $_helper */
        $_helper = Mage::helper('catalog/output');
        return $_helper->productAttribute($product, $product->getName(), 'name');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public static function media($product)
    {
        Mage::unregister('product');
        Mage::register('product', $product);
        $imageBlock = Mage::app()->getLayout()->createBlock('catalog/product_view_media');
        $imageBlock->setTemplate('catalog/product/view/media.phtml');
        return $imageBlock->toHtml();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public static function categoryImage($product)
    {
        $imageBlock = Mage::app()->getLayout()->createBlock('core/template');
        $imageBlock->setProduct($product);
        $imageBlock->setTemplate('aw_colorswatches/catalog/category/item/image.phtml');
        return $imageBlock->toHtml();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public static function shortDescription($product)
    {
        /** @var Mage_Catalog_Helper_Output $_helper */
        $_helper = Mage::helper('catalog/output');
        return $_helper->productAttribute(
            $product, nl2br($product->getShortDescription()), 'short_description'
        );
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public static function fullDescription($product)
    {
        Mage::unregister('product');
        Mage::register('product', $product);
        $descriptionBlock = Mage::app()->getLayout()->createBlock('catalog/product_view_description');
        $descriptionBlock->setTemplate('catalog/product/view/description.phtml');
        return $descriptionBlock->toHtml();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public static function boxAdditional($product)
    {
        Mage::unregister('product');
        Mage::register('product', $product);
        $additionalBlock = Mage::app()->getLayout()->createBlock('catalog/product_view_attributes');
        $additionalBlock->setTemplate('catalog/product/view/attributes.phtml');
        return $additionalBlock->toHtml();
    }
}