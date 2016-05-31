<?php
class Dealer4dealer_Exactonline_Block_Adminhtml_Exactonline_Setting_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct(){
        parent::__construct();
        $this->setId('exactonline_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('exactonline')->__('Exact Online Connector'));
    }

    protected function _beforeToHtml(){
        $this->addTab('form_section', array(
            'label' => Mage::helper('exactonline')->__('Setting Information'),
            'title' => Mage::helper('exactonline')->__('Setting Information'),
            'content' => $this->getLayout()->createBlock('exactonline/adminhtml_exactonline_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}