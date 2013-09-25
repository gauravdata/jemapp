<?php
class Affiliate4You_SaleTracking_Block_Trackingcode extends Mage_Core_Block_Text
{    
    protected function _toHtml()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
        }
        
        if ($order->getId()) {
            $orderAmount = $order->getBaseGrandTotal();
            $shippingAmount = $order->getBaseShippingAmount();
            $finalAmount = number_format($orderAmount - $shippingAmount, 2, ',', '');
            $orderNumber = $order->getIncrementId();
            $address = $order->getBillingAddress();
            $customerName = $address->getFirstname()." ".$address->getLastname();
            $campagne = Mage::getStoreConfig('affiliate4you/campagne/campagnenummer');
            
            $reportUrl = 'https://www.affiliate4you.nl/transactie_rapport.php'.
                         '?campagne='.$campagne.
           				 '&orderbedrag='.$finalAmount.
            			 '&ordernummer='.$orderNumber.
            			 '&klantnaam='.$customerName;
            
            $text = '<img src="'.$reportUrl.'" border="0" width="1" height="1" />';
            
            $this->addText("
            <!-- START AFFILIATE4YOU TRACKINGCODE -->\n
            ".$text."\n
            <!-- END AFFILIATE4YOU TRACKINGCODE -->\n
            ");
        }

        return parent::_toHtml();
    }
}