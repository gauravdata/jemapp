<?php
/**
 * Rewrite to add support for the billink fee tax
 */
class Shopworks_Billink_Block_Fee_Order_Tax extends Mage_Adminhtml_Block_Sales_Order_Totals_Tax
{
    /**
     * Get full information about taxes applied to order
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        /** @var $source Mage_Sales_Model_Order */
        $order = $this->getOrder();
        
        if ($order instanceof Mage_Sales_Model_Order)
        {
            return $this->_addBillinkTaxInfo($order);
        }
    }
    
    /**
     * Reproduce order tax info to add billink fee tax to totals
     * 
     * @param Mage_Sales_Model_Order $order
     * @return array, tax info
     */
    protected function _addBillinktaxInfo(Mage_Sales_Model_Order $order)
    {
        $rates = Mage::getModel('sales/order_tax')->getCollection()->loadByOrder($order)->toArray();
        $info = Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
        return $info;
    }

}