<?php

class Mage_Pap_Block_Paypal_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $standard = Mage::getModel('paypal/standard');
        
        //*******************************************
        // BEGIN PAP TRACKING EDITS
        //*******************************************
        $paypal_url = '';
        if (method_exists($standard, 'getPaypalUrl'))
        {
          // before 1.4
          $paypal_url = $standard->getPaypalUrl();
        }
        else
        {
          // after 1.4
          $paypal_url = $standard->getConfig()->getPaypalUrl();
        }
        
        //*******************************************
        // END PAP TRACKING EDITS
        //*******************************************
        
        $form = new Varien_Data_Form();
        $form->setAction($paypal_url)
            ->setId('paypal_standard_checkout')
            ->setName('paypal_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->getStandardCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Paypal in a few seconds.');

        //*******************************************
        // BEGIN PAP TRACKING EDITS
        //*******************************************
        
        $config = Mage::getSingleton('pap/config'); // we'll need this
        
        // Get the quote
        $quote = $standard->getQuote();
        if ($quote)
        {
          // from there, get the quote ID
          if ($quote instanceof Mage_Sales_Model_Quote) {
              $quoteId = $quote->getId();
          } else {
              $quoteId = $quote;
          }
  
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
            
            // Add a special field to hold the affiliate tracking data
            $form->addField('pap_ab78y5t4a', 'hidden', array('name'=>'custom', 'id'=>'pap_ab78y5t4a', 'value'=>json_encode($items)));
          }
        }
        
        //*******************************************
        // END PAP TRACKING EDITS
        //*******************************************
        
        $html.= $form->toHtml();
        
        //*******************************************
        // BEGIN PAP TRACKING EDITS
        //*******************************************
        
        $id = 'pap_x2s6df8d';
        global $have_pap_x2s6df8d;
        if (isset($have_pap_x2s6df8d) && $have_pap_x2s6df8d)
        {
          $id = 'pap_x2s6df8d_salestrack';
        }
        $have_pap_x2s6df8d = true;
        
        // Append the script to make the affiliate tracking work
        $html.= '<script id="'.$id.'" src="'.$config->getRemotePath().'/scripts/salejs.php" type="text/javascript"></script>';
        $html.= '<script type="text/javascript">';

        // Unfortunately, both the sale data, and the cookie data, must be passed through in the same field
        // but the sale data is known server side, and the cookie data can only be properly retrieved
        // client side. As a result, we have to stuff the sale data into the field, then the cookie can be
        // stuffed in after a delimiter. The delimiter must not occur in either set of data. The next line
        // specifies the delimiter. THIS MUST BE IDENTICAL TO THE COUNTERPART IN Paypal.php!!!!
        $html.= 'PostAffTracker.setAppendValuesToField(\'~~~a469ccb0-767c-4fed-96de-99427e1783aa~~~\');';

        // Write the tracking data to the PayPal form, rather than registering the sale immediately
        $html.= 'PostAffTracker.writeCookieToCustomField(\'pap_ab78y5t4a\');';
        $html.= '</script>';
        
        //*******************************************
        // END PAP TRACKING EDITS
        //*******************************************
        
        // Code to do the redirect
        $html.= '<script type="text/javascript">document.getElementById("paypal_standard_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}