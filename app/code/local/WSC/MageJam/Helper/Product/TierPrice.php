<?php

class WSC_MageJam_Helper_Product_TierPrice extends Mage_Core_Helper_Abstract
{
    /**
     * Returns price info as array (needed for api)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getTierPriceInfo(Mage_Catalog_Model_Product $product)
    {
        $priceData = $product->getTierPrice();

        if (!isset($priceData) || !is_array($priceData)) {
            return array();
        }

        $result = array();
        foreach ($priceData as $price) {
            $result[] = $this->_priceToArray($product, $price);
        }

        return $result;
    }

    /**
     * Converts tier price to api array data
     *
     * @param array $price
     * @return array
     */
    protected function _priceToArray(Mage_Catalog_Model_Product $product, $price)
    {

        $result = array(
            'customer_group_id' => $price['cust_group'],
            'website'           => $price['website_id'],
            'qty'               => $price['price_qty'],
            'price'             => $this->_preparePrice($product, $price['price'])
        );

        return $result;
    }

    /**
     * Calculation real price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $optionPrice
     * @return mixed
     */
    protected function _preparePrice(Mage_Catalog_Model_Product $product, $optionPrice)
    {
        $tierPriceWithTax = $optionPrice;

        // If not Bundle product then calculate tax. Bundle always return percentage
        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {

            /* @var $taxHelper MageJam_Product_Helper */
            $tierPriceWithTax = Mage::helper('magejam/product')->calculatePriceIncludeTax($product, $optionPrice);

        }

        return (string) $tierPriceWithTax;

    }
}