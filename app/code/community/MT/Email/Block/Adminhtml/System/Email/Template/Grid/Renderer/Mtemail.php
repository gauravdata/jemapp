<?php

class MT_Email_Block_Adminhtml_System_Email_Template_Grid_Renderer_Mtemail
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper("adminhtml");

        if ($row->getIsMtemail()) {
            $out = $helper->__('Yes');
        } else {
            $out = $helper->__('No');
        }

        return $out;
    }

}