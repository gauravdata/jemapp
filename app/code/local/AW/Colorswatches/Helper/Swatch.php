<?php

class AW_Colorswatches_Helper_Swatch
{
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    public function getSwatchAttributeCollectionForProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$product->isConfigurable()) {
            return array();
        }
        $attributeCollection = $this->getConfigurableAttributeCollectionForProduct($product);
        /** @var AW_Colorswatches_Model_Resource_Swatchattribute_Collection $swatchAttributeCollection */
        $swatchAttributeCollection = Mage::getModel('awcolorswatches/swatchattribute')->getCollection();
        $swatchAttributeCollection
            ->addIsEnabledFilter()
            ->addAttributeIdsFilter($attributeCollection->getColumnValues('attribute_id'))
        ;
        return $swatchAttributeCollection;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    public function getConfigurableAttributeCollectionForProduct(Mage_Catalog_Model_Product $product)
    {
        return $product->getTypeInstance(true)->getConfigurableAttributes($product);
    }

    /**
     * @param AW_Colorswatches_Model_Resource_Swatchattribute_Collection $swatchCollection
     * @param int $attributeId
     *
     * @return AW_colorswatches_Model_Swatch|null
     */
    public function getSwatchAttributeFromCollectionByAttributeId($swatchCollection, $attributeId)
    {
        return $swatchCollection->getItemByColumnValue('attribute_id', $attributeId);
    }

    /**
     * @param AW_Colorswatches_Model_Swatchattribute $swatchAttribute
     * @param Mage_Catalog_Model_Product $product
     * @param int $imgWidth
     * @param int $imgHeight
     * @param int $tooltipWidth
     * @param int $tooltipHeight
     *
     * @return array
     */
    public function getOptionDataForSwatch(
        AW_Colorswatches_Model_Swatchattribute $swatchAttribute, Mage_Catalog_Model_Product $product,
        $imgWidth = 100, $imgHeight = 100, $tooltipWidth = 300, $tooltipHeight = 300
    ) {
        $totalAttributeCount = count($this->getConfigurableAttributeCollectionForProduct($product));
        $isCanOverrideWithChild = $swatchAttribute->getIsOverrideWithChild() && $totalAttributeCount === 1;

        $collection = $swatchAttribute->getSwatchCollection();
        $childProducts = $this->_getAssociatedProductList($product);
        $result = array();
        foreach($collection as $swatch) {
            /** @var AW_Colorswatches_Model_Swatch $swatch */
            $image = AW_Colorswatches_Helper_Image::resizeImage($swatch->getImage(), $imgWidth, $imgHeight);
            $ttImage = AW_Colorswatches_Helper_Image::resizeImage($swatch->getImage(), $tooltipWidth, $tooltipHeight);
            $productList = array();
            $notSaleableList = array();
            foreach($childProducts as $child) {
                $value = $child->getData($swatchAttribute->getAttributeModel()->getAttributeCode());
                if ($value !== $swatch->getOptionId()) {
                    continue;
                }
                $productList[] = $child->getId();
                if (!$child->isSaleable()) {
                    $notSaleableList[] = $child->getId();
                }
                if ($isCanOverrideWithChild) {
                    $image = $this->_getProductImage($child, $imgWidth, $imgHeight);
                    $ttImage = $this->_getProductImage($child, $tooltipWidth, $tooltipHeight);
                }
            }
            $result[$swatch->getOptionId()] = array(
                'title'        => $swatch->getOptionLabel(),
                'img'          => $image,
                'tooltipImg'   => $ttImage,
                'products'     => $productList,
                'not_saleable' => $notSaleableList,
                'sort_order'   => $swatch->getSortOrder(),
            );
        }
        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    public function getOptionDataForAttribute(
        Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute, Mage_Catalog_Model_Product $product
    )
    {
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getAttributeId())
            ->setPositionOrder()
        ;
        $result = array();
        $childProducts = $this->_getAssociatedProductList($product);
        foreach ($optionCollection as $option) {
            $productList = array();
            foreach($childProducts as $child) {
                $value = $child->getData($attribute->getProductAttribute()->getAttributeCode());
                if ($value !== $option->getId()) {
                    continue;
                }
                $productList[] = $child->getId();
            }
            $result[$option->getId()] = array(
                'products'     => $productList,
            );
        }
        return $result;
    }


    /**
     * @return string
     */
    public function getAjaxUpdateUrl()
    {
        return Mage::getUrl('awcolorswatches/ajax/productInfo');
    }

    /**
     * @param Mage_Catalog_Model_Product    $product
     *
     * @return array
     */
    protected function _getAssociatedProductList(
        Mage_Catalog_Model_Product $product
    ) {
        if (!$product->isConfigurable()) {
            return array();
        }
        return $product->getTypeInstance(true)->getUsedProducts(null, $product);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    protected function _getProductImage(Mage_Catalog_Model_Product $product, $width = 100, $height = 100)
    {
        return Mage::helper('catalog/image')->init($product, 'image')->resize($width, $height)->__toString();
    }
}