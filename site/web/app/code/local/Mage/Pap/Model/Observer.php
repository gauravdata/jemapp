<?php
class Mage_Pap_Model_Observer
{
    public function preDispatch(Varien_Event_Observer $observer)
    {
        // It is very likely that this module was installed, but PAP
        // is not installed, and even if it is installed, it may not
        // work at all if they didn't install to the default location.
        //
        // To minimize the risk of a user installing this module and
        // incorrectly assuming that all is well, we place a message
        // in their inbox one time only warning them to configure
        // the module. The message sends them to the page with our
        // more detailed information, including links to get it all
        // done if they don't already have PAP.
        $handled = Mage::app()->loadCache('pap_config_notification_handled');
        if (!$handled)
        {
          $config = Mage::getSingleton('pap/config');
          if (!$config->IsConfigured())
          {
            // Add a message informing the user that they need to configure
            // the store for Post Affiliate Pro.
            $message = Mage::getModel('adminnotification/inbox');
            $message->setSeverity(Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR);
            $message->setDateAdded(date("c", time()));
            $message->setTitle("Affiliate Program Needs Configuration");
            $message->setDescription("The Post Affiliate Pro Connector is not yet configured. Until it is configured, it may not work, or will have severe limitations. Read the message details to finish setting up your affiliate program.");
            $message->setUrl(Mage::getUrl()."pap/adminhtml_pap/checkconfig/");
            $message->Save();
          }
          // whether or not the message was needed, we don't need to check again.
          // set the flag to prevent this from running any more.
          $readNode = Mage::getConfig()->getNode('global/resources/pap_read/connection');
          Mage::app()->saveCache(1, 'pap_config_notification_handled', array(Mage_Core_Model_Config::CACHE_TAG));
        }
    }

    public function order_success_page_view($observer)
    {
        $quoteId = Mage::getSingleton('checkout/session')->getLastQuoteId();
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('pap_saletracking');
        if ($quoteId && ($block instanceof Mage_Core_Block_Abstract)) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $block->setQuote($quote);
        }
    }

    public function order_modified($observer)
    {
      // grab the event and order information
      $event = $observer->getEvent();
      $order = $event->getOrder();
      
      // get the configuration, and quit now if it isn't set up with database access
      $config = Mage::getSingleton('pap/config');
      if (!$config->IsConfigured())
      {
        return;
      }
      try
      {
        if ($order->getBaseTotalPaid() >= .0001 && // we were paid
            $order->getBaseTotalPaid()-($order->getBaseTotalRefunded()+$order->getBaseTotalCanceled()) < .0001)
        {
          // The whole order has been refunded
          // find the orders in the PAP database and mark them as declined
          Mage::getModel('pap/pap')->SetOrderStatus($order, "D");
        }
        else if ($order->getBaseTotalPaid() >= .0001 && // we were paid
                ($order->getBaseTotalRefunded() >= .0001 || $order->getBaseTotalCanceled() >= .0001)) // partial refund
        {
          // The order was partially refunded
          // find the orders in the PAP database and mark them as pending (need admin review still)
          Mage::getModel('pap/pap')->SetOrderStatus($order, "P");
        }
        else if($order->getStatus() == 'holded') // put on hold
        {
          // find the orders in the PAP database and mark them as pending
          Mage::getModel('pap/pap')->SetOrderStatus($order, "P");
        }
        else if($order->getStatus() == 'complete') // completed. (refunds were handled above, so we can ignore that usage)
        {
          if ($order->getBaseTotalPaid() >= .0001) // we were paid
          {
            // find the orders in the PAP database and mark them as complete
            Mage::getModel('pap/pap')->SetOrderStatus($order, "A");
          }
          else // we were NOT paid
          {
            // find the orders in the PAP database and mark them as declined
            Mage::getModel('pap/pap')->SetOrderStatus($order, "D");
          }
        }
        else if($order->getStatus() == 'canceled')
        {
          // find the orders in the PAP database and mark them as complete
          Mage::getModel('pap/pap')->SetOrderStatus($order, "D");
        }
      } catch (Exception $e) {
        // don't abort, just warn the user
        Mage::getSingleton('adminhtml/session')->addWarning("Could not communicate order status change to PAP. Your API file may not match your PAP installation.");
      } catch(Gpf_Exception $e) {
        // don't abort, just warn the user
        Mage::getSingleton('adminhtml/session')->addWarning("Could not communicate order status change to PAP. Your API file may not match your PAP installation.");
      }
        
      return $this;
    }
}
?>