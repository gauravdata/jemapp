<?php
class AW_Storecredit_Model_Sales_Total_Pdf_Storecredit extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    public function getTotalsForDisplay()
    {
        $_result = array();
        if (count($this->getSource()->getAwStorecredit()) > 0) {
            $storeCredits = $this->getSource()->getAwStorecredit();
            $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
            $_storeCreditTotals = array();
            foreach ($storeCredits as $storeCredit) {
                $_storeCreditTotals['aw_storecredit_' . $storeCredit->getStorecreditId()] = array (
                    'amount'    => '-' . $this->getOrder()->formatPriceTxt($storeCredit->getStorecreditAmount()),
                    'label'     => Mage::helper('aw_storecredit')->__('Store Credit'),
                    'font_size' => $fontSize,
                );
            }
            $_result = $_storeCreditTotals;
        }
        return $_result;
    }
}