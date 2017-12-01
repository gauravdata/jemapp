<?php
class AW_Storecredit_Block_Adminhtml_Widget_Grid_Column_Renderer_Additional
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        if (null === $row->getData('additional_info')) {
            return '';
        }

        $addition = $row->getData('additional_info');
        if (!is_array($addition)
            || !array_key_exists('message_type', $addition)
            || !array_key_exists('message_data', $addition)
        ) {
            return '';
        }
        $message = Mage::helper('aw_storecredit')->prepareMessage($addition);

        return $message;
    }


}