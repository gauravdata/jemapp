<?php

class MT_Email_Block_Email_Block_Sales_Order_Info
    extends MT_Email_Block_Email_Block_Template
{
    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getOrder()
    {
        $order = parent::getOrder();
        if (!$order) {
            $order = Mage::helper('mtemail')->getDemoOrder();
            $this->setOrder($order);
        }

        return $order;
    }

    public function getPaymentHtml()
    {
        $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getOrder()->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($this->getStoreId());
        return $paymentBlock->toHtml();
    }
}