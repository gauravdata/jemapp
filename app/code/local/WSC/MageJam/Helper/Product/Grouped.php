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

        /* @var $taxHelper MageJam_Product_Helper */
        $selectionPriceWithTax = Mage::helper('magejam/product')->calculatePriceIncludeTax($item, $item->getFinalPrice());

        $result['price'] = (string) $selectionPriceWithTax;

        $result['is_saleable'] = (int) $item->isSaleable();
        $result['position'] = (int) $item->getPosition();
        $result['qty'] = $item->getQty();
        $result['stock'] = $item->getStockItem()->getQty();
        $result['is_in_stock'] = $item->getStockItem()->getIsInStock();

        return $result;
    }
}