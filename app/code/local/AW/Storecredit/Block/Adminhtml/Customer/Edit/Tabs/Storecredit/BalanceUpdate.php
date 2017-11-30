<?php

class AW_Storecredit_Block_Adminhtml_Customer_Edit_Tabs_Storecredit_BalanceUpdate extends Mage_Adminhtml_Block_Widget_Form
{
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('aw_');

        $fieldset = $form->addFieldset(
            'storecredit_fieldset', array('legend' => Mage::helper('aw_storecredit')->__('Update Store Credit Balance'))
        );

        $fieldset->addField(
            'update_storecredit',
            'text',
            array(
                 'label' => Mage::helper('aw_storecredit')->__('Update Store Credit'),
                 'name'  => 'aw_update_storecredit',
                 'note'  => Mage::helper('aw_storecredit')->__('Enter a negative number to subtract from the balance'),
                 'class'    => 'validate-number',
            )
        );

        $fieldset->addField(
            'comment',
            'text',
            array(
                 'label' => Mage::helper('aw_storecredit')->__('Comment'),
                 'name'  => 'aw_update_storecredit_comment',
                 'note'     => Mage::helper('aw_storecredit')->__('Visible to customer')
            )
        );

        $this->setForm($form);
        return $this;
    }
}