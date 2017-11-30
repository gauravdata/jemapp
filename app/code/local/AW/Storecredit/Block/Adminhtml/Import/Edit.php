<?php
class AW_Storecredit_Block_Adminhtml_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'aw_storecredit';
        $this->_controller = 'adminhtml_import';
        $this->_headerText = $this->getHeaderText();
        parent::__construct();

        $this->removeButton('back')
            ->removeButton('reset')
            ->_updateButton('save', 'label', $this->__('Import'));
    }

    public function getHeaderText()
    {
        $title = $this->__('Import Store Credits from CSV File');
        return $title;
    }

}