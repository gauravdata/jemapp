<?php

class MT_Email_Block_Email_Block_Sales_Order_Totals
    extends Mage_Core_Block_Template
{

    protected $_creditmemo = null;

    protected $_order = null;

    protected $_invoice = null;

    protected $_shipment = null;

    public function __construct()
    {
        parent::__construct();
        $this->setData('area','frontend');
    }

    protected function _beforeToHtml()
    {
        $this->setTemplate('mt/email/theme/'.$this->getParentBlock()->getTheme().'/'.$this->getTemplate());
        return parent::_beforeToHtml();
    }

    public function getVarModel()
    {
        $varModel = $this->getParentBlock()->getVarModel();
        Mage::unregister('current_email_block_var_model');
        Mage::register('current_email_block_var_model', $varModel);
        return $varModel;
    }

    public function getTotals()
    {
        switch ($this->getTotalsType()) {
            case 'order':
                return $this->getOrderTotals();
                break;
            case 'invoice':
                return $this->getInvoiceTotals();
                break;
            case 'creditmemo':
                return $this->getCreditmemoTotals();
                break;
            case 'shipment':
                return $this->getShipmentTotals();
                break;
        }

        return array();
    }

    public function getTotalsBlock()
    {
        switch ($this->getTotalsType()) {
            case 'order':
                return $this->getChild('order_totals');
                break;
            case 'invoice':
                return $this->getChild('invoice_totals');
                break;
            case 'creditmemo':
                return $this->getChild('creditmemo_totals');
                break;
        }

        return null;
    }

    public function getCreditmemoTotals()
    {
        $child = $this->getChild('creditmemo_totals')
            ->setOrder($this->getOrder())
            ->setCreditmemo($this->getCreditmemo());
        $child->toHtml();
        return $child->getTotals();
    }

    public function getShipmentTotals()
    {
        $child = $this->getChild('shipment_totals')
            ->setOrder($this->getOrder())
            ->setShipment($this->getShipment());
        $child->toHtml();
        return $child->getTotals();
    }

    public function getOrderTotals()
    {
        $child = $this->getChild('order_totals')
            ->setOrder($this->getOrder());
        $child->toHtml();
        return $child->getTotals();
    }

    public function getInvoiceTotals()
    {
        $child = $this->getChild('invoice_totals')
            ->setOrder($this->getOrder())
            ->setInvoice($this->getInvoice());
        $child->toHtml();
        return $child->getTotals();
    }

    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            return $this->getOrder()->formatPrice($total->getValue());
        }
        return $total->getValue();
    }

    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if ($this->_getData('creditmemo')) {
                $this->_creditmemo = $this->_getData('creditmemo');
            } elseif (Mage::registry('current_creditmemo')) {
                $this->_creditmemo = Mage::registry('current_creditmemo');
            } else {
                $this->_creditmemo =  Mage::helper('mtemail')->getDemoCreditMemo();
            }
        }

        return $this->_creditmemo;
    }

    public function getShipment()
    {
        if ($this->_shipment === null) {
            if ($this->_getData('shipment')) {
                $this->_shipment = $this->_getData('shipment');
            } elseif (Mage::registry('current_shipment')) {
                $this->_shipment = Mage::registry('current_shipment');
            } else {
                $this->_shipment =  Mage::helper('mtemail')->getDemoShipment();
            }
        }

        return $this->_shipment;
    }

    public function getInvoice()
    {
        if ($this->_invoice === null) {
            if (Mage::registry('mt_editor_edit_mode')) {
                $this->_invoice =  Mage::helper('mtemail')->getDemoInvoice();
            } else {
                if ($this->_getData('invoice')) {
                    $this->_invoice = $this->_getData('invoice');
                } elseif ($this->getParentBlock()->getInvoice()) {
                    $this->_invoice = $this->getParentBlock()->getInvoice();
                } elseif (Mage::registry('current_invoice')) {
                    $this->_invoice = Mage::registry('current_invoice');
                } else if ($this->hasData('order')) {
                    $this->_invoice = $this->getOrder()->getInvoiceCollection()->getFirstItem();
                }
            }

            if ($this->_invoice == null) {
                Mage::helper('mtemail')->log('The Invoice is missing. Area: '.(Mage::registry('mt_editor_edit_mode')?'Adminhtml':'Frontend'));
            }
        }

        return $this->_invoice;
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

    public function getTotalHtml($childName)
    {
        $totalBlock = $this->getTotalsBlock()->getChild($childName);

        if ($childName == 'tax') {
            $this->updateTemplatePath($totalBlock);
        }
        return $totalBlock->toHtml();
    }

    public function updateTemplatePath($block)
    {
        $block->setTemplate('mt/email/theme/'.$this->getTheme().'/block/sales/order/'.$block->getTemplate());
    }

}