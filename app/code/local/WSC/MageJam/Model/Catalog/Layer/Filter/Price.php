<?php

class WSC_MageJam_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    /**
     * This method has been rewritten because we don't need span container in api output
     *
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return string
     */
    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $store = Mage::app()->getStore();
        $formattedFromPrice  = $store->formatPrice($fromPrice, false);
        if ($toPrice === '') {
            return Mage::helper('catalog')->__('%s and above', $formattedFromPrice);
        }
        if ($fromPrice == $toPrice && Mage::app()->getStore()->getConfig(self::XML_PATH_ONE_PRICE_INTERVAL)) {
            return $formattedFromPrice;
        }
        if ($fromPrice != $toPrice) {
            $toPrice -= .01;
        }
        return Mage::helper('catalog')->__('%s - %s', $formattedFromPrice, $store->formatPrice($toPrice, false));
    }

    /**
     * This method used in magento 1.6 and below, so we need to rewrite it too
     *
     * @param   int $range
     * @param   float $value
     * @return  string
     */
    protected function _renderItemLabel($range, $value)
    {
        $store      = Mage::app()->getStore();
        $fromPrice  = $store->formatPrice(($value-1)*$range, false);
        $toPrice    = $store->formatPrice($value*$range, false);

        return Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice);
    }
}