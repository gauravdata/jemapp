<?php

class CommerceExtensions_Advancedcustomerimportexport_Block_System_Convert_Gui_Edit_Tab_Wizard extends Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('advancedcustomerimportexport/system/convert/profile/wizard.phtml');
    }
	public function getAttributes($entityType)
    {
        if (!isset($this->_attributes[$entityType])) {
            switch ($entityType) {
                case 'customer':
					$attributes = Mage::getSingleton('customer/convert_parser_customer')
                        ->getExternalAttributes();
                    break;
            }
            array_splice($attributes, 0, 0, array(''=>$this->__('Choose an attribute')));
            switch ($entityType) {
                case 'customer':
					$attributes['password'] = "password";
                    break;
            }
            $this->_attributes[$entityType] = $attributes;
        }
        return $this->_attributes[$entityType];
    }
}

