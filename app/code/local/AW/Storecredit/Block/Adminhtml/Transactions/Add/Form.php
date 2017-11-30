<?php

class AW_Storecredit_Block_Adminhtml_Transactions_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $helper = Mage::helper('aw_storecredit');

        $form = new Varien_Data_Form(
            array(
                 'id'      => 'transaction_add_form',
                 'action'  => $this->getUrl('*/*/save'),
                 'method'  => 'post',
                 'enctype' => 'multipart/form-data',
            )
        );

        $fieldset = $form->addFieldset('main_group', array('legend' => Mage::helper('aw_storecredit')->__('Fields')));

        $fieldset->addField(
            'balance_change',
            'text',
            array(
                'label'     => $helper->__('Store Credit Balance Change'),
                'required'  => true,
                'name'      => 'balance_change',
                'class'     => 'validate-number',
                'note'      => Mage::helper('aw_storecredit')->__('Enter a negative number to subtract from the balance')
            )
        );

        $fieldset->addField(
            'comment',
            'text',
            array(
                 'label'    => $helper->__('Comment'),
                 'required' => true,
                 'name'     => 'comment',
                 'note'     => Mage::helper('aw_storecredit')->__('Visible to customer')
            )
        );

        $fieldset->addField(
            'selected_customers',
            'hidden',
            array(
                 'name' => 'selected_customers',
            )
        );

        $fieldset->addField(
            'internal_customer',
            'hidden',
            array(
                'name' => 'internal_customer',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}