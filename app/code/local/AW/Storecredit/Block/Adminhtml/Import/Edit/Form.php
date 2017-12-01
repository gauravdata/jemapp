<?php
class AW_Storecredit_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id'      => 'edit_form',
                'name'      => 'edit_form',
                'action'  => Mage::helper('adminhtml')->getUrl('*/*/save'),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->addField(
            'storecredit_csv',
            'file',
            array(
                'name' => 'storecredit_csv',
                'label' => $this->__('CSV file to import:'),
            )
        );

        $form->addField(
            'action_if_customer_exist',
            'radios',
            array(
                'name' => 'action_if_customer_exist',
                'label' => $this->__('If customer has a Store Credit:'),
                'value'  => '1',
                'values' => array(
                    array('value'=>'1','label'=>'Replace balance'),
                    array('value'=>'0','label'=>'Append balance'),
                )
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}