<?php
/**
 * Created by PhpStorm.
 * User: mathijs
 * Date: 29-11-17
 * Time: 14:45
 */ 
class Twm_ExtendAwPoints_Block_Points_Catalog_Product_Points extends AW_Points_Block_Catalog_Product_Points
{
    public function getPoints()
    {
        if (is_null($this->getData('points'))) {
            try {
                $pointsSummary = 0;
                $product = Mage::registry('current_product');

                $price = $product->getData('price_by_three');

                if (is_null($price) || $price === false || empty(trim($price)))
                    $price = $product->getFinalPrice();

                $storeId = $product->getStore()->getId();
                if (Mage::helper('points/config')->getPointsCollectionOrder($storeId) == AW_Points_Helper_Config::AFTER_TAX) {
                    $price = Mage::helper('tax')->getPrice($product, $price, true);
                }
                /* Points amount after order complete */
                $pointsSummary += Mage::getModel('points/rate')
                    ->loadByDirection(AW_Points_Model_Rate::CURRENCY_TO_POINTS)
                    ->exchange($price)
                ;

                $maximumPointsPerCustomer = Mage::helper('points/config')->getMaximumPointsPerCustomer();
                if ($maximumPointsPerCustomer) {
                    $customersPoints = 0;
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    if ($customer) {
                        $customersPoints = Mage::getModel('points/summary')->loadByCustomer($customer)->getPoints();
                    }
                    if ($pointsSummary + $customersPoints > $maximumPointsPerCustomer) {
                        $pointsSummary = $maximumPointsPerCustomer - $customersPoints;
                    }
                }
                $this->setData('points', $pointsSummary);
            } catch (Exception $e) {

            }
        }
        return $this->getData('points');
    }

}