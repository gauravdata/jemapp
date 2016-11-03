<?php

class MT_Email_Block_Email_Block_Sales_Shipping_Tracking
    extends MT_Email_Block_Email_Block_Template
{

    protected $_order = null;

    protected $_shipment = null;

    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getShipment()
    {
        if ($this->_shipment === null) {
            if (Mage::registry('mt_editor_edit_mode')) {
                $this->_shipment =  Mage::helper('mtemail')->getDemoShipment();
            } else {
                if ($this->_getData('shipment')) {
                    $this->_shipment = $this->_getData('shipment');
                } elseif ($this->getParentBlock()->getShipment()) {
                    $this->_shipment = $this->getParentBlock()->getShipment();
                } elseif (Mage::registry('current_shipment')) {
                    $this->_shipment = Mage::registry('current_shipment');
                } else if ($this->hasData('order')) {
                    $this->_shipment = $this->getOrder()->getShipmentsCollection()->getFirstItem();
                }
            }

            if ($this->_shipment == null) {
                Mage::helper('mtemail')->log('The shipment is missing. Area: '.(Mage::registry('mt_editor_edit_mode')?'Adminhtml':'Frontend'));
            }
        }

        return $this->_shipment;
    }

    public function getOrder()
    {

        if ($this->_order === null) {
            if ($this->_getData('order')) {
                $this->_order = $this->_getData('order');
            } elseif (Mage::registry('current_order')) {
                $this->_order = Mage::registry('current_order');
            } else {
                $this->_order = Mage::helper('mtemail')->getDemoOrder();
            }
        }

        return  $this->_order;
    }

}