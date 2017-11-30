<?php
class AW_Storecredit_Block_Adminhtml_Import extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_import';
        $this->_blockGroup = 'aw_storecredit';
        $this->_headerText = Mage::helper('aw_storecredit')->__('Import Store Credits');
        return parent::__construct();
    }
}