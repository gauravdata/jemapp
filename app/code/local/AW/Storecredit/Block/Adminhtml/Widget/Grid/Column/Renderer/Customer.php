<?php
class AW_Storecredit_Block_Adminhtml_Widget_Grid_Column_Renderer_Customer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        if (null === $row->getData('name')) {
            return '';
        }
        $name = $row->getData('name');
        if (null !== $row->getData('customer.entity_id')) {
            $customerId = $row->getData('customer.entity_id');
            $name = '<a href="'. $this->getUrl('adminhtml/customer/edit', array('id' => $customerId, 'storecredit' => true)).'">'.$name.'</a>';
        }

        return $name;
    }
}