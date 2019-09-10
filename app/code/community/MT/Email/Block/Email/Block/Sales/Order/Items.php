<?php

class MT_Email_Block_Email_Block_Sales_Order_Items
    extends Mage_Sales_Block_Order_Email_Items
{

    public function __construct()
    {
        parent::__construct();
        $this->setData('area','frontend');
    }

    protected function _beforeToHtml()
    {
        $this->setTemplate('mt/email/theme/'.$this->getParentBlock()->getTheme().'/'.$this->getTemplate());
        return parent::_toHtml();
    }

    public function getVarModel()
    {
        $varModel = $this->getParentBlock()->getVarModel();
        Mage::unregister('current_email_block_var_model');
        Mage::register('current_email_block_var_model', $varModel);
        return $varModel;
    }

    public function getItems()
    {
        if ($this->hasData('creditmemo')) {
            return $this->getCreditmemo()->getAllItems();
        } else {
            return $this->getOrder()->getAllItems();
        }
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

    public function getCreditmemo()
    {
        $creditmemo = parent::getCreditmemo();
        if (!$creditmemo) {
            $creditmemo = Mage::helper('mtemail')->getDemoCreditmemo();
            $this->setCreditmemo($creditmemo);
        }

        return $creditmemo;
    }
}