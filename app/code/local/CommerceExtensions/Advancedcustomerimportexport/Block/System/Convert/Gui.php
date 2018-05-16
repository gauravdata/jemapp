<?php

class CommerceExtensions_Advancedcustomerimportexport_Block_System_Convert_Gui extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'system_convert_gui';
        $this->_blockGroup = 'advancedcustomerimportexport';
        
        $this->_headerText = Mage::helper('advancedcustomerimportexport')->__('Profiles');
        $this->_addButtonLabel = Mage::helper('advancedcustomerimportexport')->__('Add New Profile');

        parent::__construct();
    }
}