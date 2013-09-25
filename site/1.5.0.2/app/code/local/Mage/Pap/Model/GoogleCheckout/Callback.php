<?php

// Make absolutely sure we have the prerequisite
require_once BP . DS . 'app/code/core/Mage/GoogleCheckout/Model/Api/Xml/Callback.php';

class Mage_Pap_Model_GoogleCheckout_Callback extends Mage_GoogleCheckout_Model_Api_Xml_Callback
{
    protected function _responseNewOrderNotification()
    {
      // first, let the parent do it's thing. This will create the order record, which
      // we will then retrieve and use to track the affiliate sale.
      parent::_responseNewOrderNotification();

      // Give PAP access to the data so it can handle any needed commissions
      try // If "Something Bad" happens, it isn't worth dying over
      {
        $papModel = Mage::getModel('pap/pap');
        $cookieValue = $this->getData('root/shopping-cart/merchant-private-data/pap-cookie-data/VALUE');

        $config = Mage::getSingleton('pap/config'); // we'll need this
        
        // Get the quote id
        $quoteId = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');
        
        if ($quoteId)
        {
          // Get the order(s) for the quote
          $orders = Mage::getResourceModel('sales/order_collection')
              ->addAttributeToFilter('quote_id', $quoteId)
              ->load();
          
          // get raw data to submit from the collection of orders
          $items = array();
          foreach ($orders as $order)
          {
            if (!$order){continue;}
    
            if (!$order instanceof Mage_Sales_Model_Order) {
                $order = Mage::getModel('sales/order')->load($order);
            }
            
            if (!$order){continue;}
            
            $order = Mage::getModel('pap/pap')->getOrderSaleDetails($order);
            array_splice($items, -1, 0, $order);
          }
          
          $papModel->registerSaleDetails($items, isset($cookieValue) ? $cookieValue : null);
        }
      }
      catch (Exception $e)
      {
        Mage::log('Caught exception while trying to log GoogleCheckout sale: '.$e->getMessage()."\n");
      }
    }
}
