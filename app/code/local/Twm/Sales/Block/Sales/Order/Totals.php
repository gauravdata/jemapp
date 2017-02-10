<?php
/**
 * Created by PhpStorm.
 * User: freek
 * Date: 10-2-17
 * Time: 9:49
 */ 
class Twm_Sales_Block_Sales_Order_Totals extends Mage_Sales_Block_Order_Totals {

    protected function _initTotals()
    {
        if ($this->getSource()->getDiscountAmount() == 0 && ($this->getSource()->getDiscountDescription() || $this->getSource()->getCouponCode())) {
            $this->getSource()->setDiscountAmount(0.00000000000001);
        }

        parent::_initTotals();
    }

}