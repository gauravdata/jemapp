<?php
class Shopworks_Billink_Block_Fee_Invoice_Totals extends Mage_Core_Block_Abstract
{
    private $_invoice;

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

        $this->_invoice = $parent->getInvoice();

        //When the Klarna module is used, this invoice cannot be extracted from the parent block. In that case we can
        //fetch it from the registry
        if(!$this->_invoice)
        {
            $this->_invoice = Mage::registry('current_invoice');
        }

        if ($this->_invoice->getBillinkFee() < 0.01)
        {
            return $this;
        }

        $feeLabel = Mage::getStoreConfig('payment/billink/fee_label');

        $billinkFee = new Varien_Object();
        $billinkFee->setLabel($feeLabel);
        $billinkFee->setValue($this->_invoice->getBillinkFee());
        $billinkFee->setBaseValue($this->_invoice->getBaseBillinkFee());
        $billinkFee->setCode(Shopworks_Billink_Model_Payment_Method::TOTAL_FEE_CODE);

        if ($display === 1)//excl tax
        {
            $parent->addTotalBefore($billinkFee, 'shipping');
        }
        elseif ($display === 2)//incl tax
        {
            $billinkFee->setValue($this->_invoice->getBillinkFeeInclTax());
            $billinkFee->setBaseValue($this->_invoice->getBaseBillinkFeeInclTax());

            $parent->addTotalBefore($billinkFee, 'shipping');
        }
        else//display incl and excl
        {
            $feeInclLabel = $feeLabel . Mage::helper('billink')->__(' (Incl. Tax)');
            $feeExclLabel = $feeLabel . Mage::helper('billink')->__(' (Excl. Tax)');

            $billinkFee->setLabel($feeExclLabel);

            $billinkFeeInclTax = new Varien_Object();
            $billinkFeeInclTax->setLabel($feeInclLabel);
            $billinkFeeInclTax->setValue($this->_invoice->getBillinkFeeInclTax());
            $billinkFeeInclTax->setBaseValue($this->_invoice->getBaseBillinkFeeInclTax());
            $billinkFeeInclTax->setCode(Shopworks_Billink_Model_Payment_Method::TOTAL_FEE_CODE_INCL_TAX);

            $parent->addTotalBefore($billinkFee, 'shipping');
            $parent->addTotalBefore($billinkFeeInclTax, 'shipping');
        }
        return $this;
    }

}
