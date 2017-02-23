<?php
/**
 * Created by PhpStorm.
 * User: freek
 * Date: 8-2-17
 * Time: 16:29
 */ 
class Twm_Sales_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals {

    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        if ($this->getSource()->getDiscountAmount() == 0 && ($this->getSource()->getDiscountDescription() || $this->getSource()->getCouponCode())) {
            $this->getSource()->setDiscountAmount(0.00000000000001);
        }

        parent::_initTotals();
    }

}