<?php

class MT_Email_Model_Adminhtml_System_Config_Source_Direction
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'ltr', 'label'=>Mage::helper('adminhtml')->__('Left to Right (LTR)')),
            array('value' => 'rtl', 'label'=>Mage::helper('adminhtml')->__('Right to Left (RTL)')),

        );
    }
}