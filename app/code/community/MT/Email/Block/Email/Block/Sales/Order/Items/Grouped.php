<?php

class MT_Email_Block_Email_Block_Sales_Order_Items_Grouped
    extends Mage_Sales_Block_Order_Email_Items_Order_Grouped
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
            $this->setTemplate('mt/email/theme/'.$theme.'/'.$this->getTemplate());
            $this->__flagTheme = true;
        }
        return parent::_beforeToHtml();
    }

    public function getVarModel()
    {
        $varModel = Mage::registry('current_email_block_var_model');
        return $varModel;
    }


}