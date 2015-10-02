<?php
class Shopworks_Billink_Block_Fee_Order_Totals extends Mage_Core_Block_Abstract
{
    /**
     * Add a total for the billink fee
     * 
     * @see app\design\adminhtml\default\default\layout\billink.xml
     * @see app\design\frontend\base\default\layout\billink.xml
     * @return Shopworks_Billink_Block_Fee_Order_Totals
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $display = (int)Mage::getStoreConfig('tax/sales_display/subtotal', Mage::app()->getStore()->getId());
        $this->_order = $parent->getOrder();

        if ($this->_order->getBillinkFee() < 0.01)
        {
            return $this;
        }

        $feeLabel = Mage::getStoreConfig('payment/billink/fee_label');

        $billinkFee = new Varien_Object();
        $billinkFee->setLabel($feeLabel);
        $billinkFee->setValue($this->_order->getBillinkFee());
        $billinkFee->setBaseValue($this->_order->getBaseBillinkFee());
        $billinkFee->setCode(Shopworks_Billink_Model_Payment_Method::TOTAL_FEE_CODE);

        if ($display === 1)// excl tax
        {
            $parent->addTotalBefore($billinkFee, 'grand_total');
        }
        elseif ($display === 2)//incl tax
        {
            $billinkFee->setValue($this->_order->getBillinkFeeInclTax());
            $billinkFee->setBaseValue($this->_order->getBaseBillinkFeeInclTax());

            $parent->addTotalBefore($billinkFee, 'grand_total');
        }
        else//display incl and excl
        {
            $feeInclLabel = $feeLabel . Mage::helper('billink')->__(' (Incl. Tax)');
            $feeExclLabel = $feeLabel . Mage::helper('billink')->__(' (Excl. Tax)');

            $billinkFee->setLabel($feeExclLabel);

            $billinkFeeInclTax = new Varien_Object();
            $billinkFeeInclTax->setLabel($feeInclLabel);
            $billinkFeeInclTax->setValue($this->_order->getBillinkFeeInclTax());
            $billinkFeeInclTax->setBaseValue($this->_order->getBaseBillinkFeeInclTax());
            $billinkFeeInclTax->setCode(Shopworks_Billink_Model_Payment_Method::TOTAL_FEE_CODE_INCL_TAX);

            $parent->addTotalBefore($billinkFee, 'grand_total');
            $parent->addTotalBefore($billinkFeeInclTax, 'grand_total');
        }

        return $this;
    }

}
