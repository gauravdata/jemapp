<?php
class AW_Storecredit_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_customer';
        $this->_blockGroup = 'aw_storecredit';
        $this->_headerText = Mage::helper('aw_storecredit')->__('Customers');
        parent::__construct();
        $this->_removeButton('add');

        return $this;
    }
}