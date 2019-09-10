<?php

class MT_Email_Block_Adminhtml_Widget_Grid_Column_Renderer_Store
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store
{
    public function render(Varien_Object $row)
    {
        return $row->getData($this->getColumn()->getIndex())==0?Mage::helper('mtemail')->__('All Store Views'):parent::render($row);
    }
}