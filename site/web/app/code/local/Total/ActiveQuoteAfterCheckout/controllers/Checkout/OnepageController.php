<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';

class Total_ActiveQuoteAfterCheckout_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    public function successAction()
    {
       foreach( Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item ){
              Mage::getSingleton('checkout/cart')->removeItem( $item->getId() )->save();
       }

       Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false);
       
       parent::successAction();
    }
}