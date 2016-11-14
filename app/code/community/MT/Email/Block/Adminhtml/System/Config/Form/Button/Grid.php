<?php

class MT_Email_Block_Adminhtml_System_Config_Form_Button_Grid
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_typeRender;

    protected $_yesnoRender;

    protected function _construct()
    {

        $this->addColumn('name', array(
            'label' => Mage::helper('mtemail')->__('Font Family'),
            'style' => 'width:500px',
        ));


        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('mtemail')->__('Add Field');

        parent::_construct();
    }

    protected function _getTypeRenderer()
    {
        if (!$this->_typeRender) {
            $this->_typeRender = Mage::app()->getLayout()->createBlock(
                'mtemail/adminhtml_system_config_form_field', '',
                array('is_render_to_js_template' => true)
            );
            $this->_typeRender->setClass('long-input');
        }
        return $this->_typeRender;
    }



    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getTypeRenderer()->calcOptionHash($row->getData('type')),
            'selected="selected"'
        );

    }

    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
    }
}