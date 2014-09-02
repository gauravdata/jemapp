<?php

class WSC_MageJam_Helper_Product_Grouped extends Mage_Core_Helper_Abstract
{
    /**
     * Returns items of grouped product according to Api Response format
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getGroupedItems(Mage_Catalog_Model_Product $product)
    {
        $items = $product->getTypeInstance(true)->getAssociatedProducts($product);

        $result = array();
        foreach($items as $item) {
            $result[] = $this->_convertItemToArray($item);
        }
        return $result;
    }

    /**
     * Converts item into array according to Api Response format
     *
     * @param Mage_Catalog_Model_Product $item
     * @return array
     */
    protected function _convertItemToArray(Mage_Catalog_Model_Product $item)
    {
        $result = array();

        $result['product_id'] = $item->getEntityId();
        $result['sku'] = $item->getSku();
        $result['name'] = $item->getName();

        /* @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper = Mage::helper('tax');
        $result['price'] = (string) $taxHelper->getPrice($item, $item->getFinalPrice(), true);

        $result['is_saleable'] = (int) $item->isSaleable();
        $result['position'] = (int) $item->getPosition();
        $result['qty'] = $item->getQty();

        return $result;
    }
}