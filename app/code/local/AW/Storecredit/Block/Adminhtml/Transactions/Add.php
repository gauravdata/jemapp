<?php

class AW_Storecredit_Block_Adminhtml_Transactions_Add extends Mage_Adminhtml_Block_Widget
{
    public function getHeaderText()
    {
        return Mage::helper('aw_storecredit')->__('Add Transaction');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'back_button',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                         'label'   => Mage::helper('aw_storecredit')->__('Back'),
                         'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                         'class'   => 'back'
                    )
                )
        );

        $this->setChild(
            'save_button',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                         'label'   => Mage::helper('aw_storecredit')->__('Save Transaction'),
                         'onclick' => "transactionAddForm.submit();",
                         'class'   => 'save'
                    )
                )
        );

        return parent::_prepareLayout();
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

    public function getForm()
    {
        return $this->getLayout()
            ->createBlock('aw_storecredit/adminhtml_transactions_add_form')
            ->toHtml();
    }

    public function getCustomersGrid()
    {
        return $this->getLayout()
            ->createBlock('aw_storecredit/adminhtml_transactions_add_customer_grid')
            ->setUseAjax(true)
            ->toHtml();
    }
}