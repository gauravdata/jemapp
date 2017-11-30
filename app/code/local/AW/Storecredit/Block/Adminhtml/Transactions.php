<?php
class AW_Storecredit_Block_Adminhtml_Transactions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_transactions';
        $this->_blockGroup = 'aw_storecredit';
        $this->_headerText = Mage::helper('aw_storecredit')->__('Transactions');
        return parent::__construct();
    }
}