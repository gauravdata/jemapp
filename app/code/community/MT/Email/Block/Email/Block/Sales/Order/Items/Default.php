<?php

class MT_Email_Block_Email_Block_Sales_Order_Items_Default
    extends Mage_Sales_Block_Order_Email_Items_Order_Default
{

    private $__flagTheme = false;

    public function __construct()
    {
        parent::__construct();
        $this->setData('area','frontend');
    }

    protected function _beforeToHtml()
    {
        if (!$this->__flagTheme) {
            $theme = Mage::registry('current_email_block_theme');
            $template = $this->getTemplate();
            $itemName = $item = $this->getItem()->getName();
            if (strlen($itemName) > 23) {
                $template = str_replace('default.phtml', 'default_long.phtml', $template);
            }

            $this->setTemplate('mt/email/theme/'.$theme.'/'.$template);
            $this->__flagTheme = true;
        }
        return parent::_beforeToHtml();
    }

    public function getVarModel()
    {
        $varModel = Mage::registry('current_email_block_var_model');
        return $varModel;
    }

    public function getOrder()
    {
        $item = $this->getItem();
        if ($item instanceof  Mage_Sales_Model_Order_Item) {
            return $item->getOrder();
        } else {
            return $item->getOrderItem()->getOrder();
        }
    }
    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function showSku()
    {
        return Mage::getStoreConfig('mtemail/template/show_sku', $this->getStoreId() );
    }

}