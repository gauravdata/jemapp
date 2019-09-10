<?php

class MT_Email_Block_Adminhtml_System_Email_Template_Grid_Renderer_Action
    extends Mage_Adminhtml_Block_System_Email_Template_Grid_Renderer_Action
{
    public function render(Varien_Object $row)
    {

        if ($row->getIsMtemail()) {
            $helper = Mage::helper("adminhtml");
            $out = '<a onclick="'."popWin(this.href,'_blank','width=800,height=700,resizable=1,scrollbars=1');return false;".'" href="'.$helper->getUrl('adminhtml/email_mteditor/preview/', array('id' => $row->getId())).'">'.$helper->__('Preview').'</a>';
        } else {
            $out = parent::render($row);
        }

        return $out;
    }

}