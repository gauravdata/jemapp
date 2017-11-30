<?php

class AW_Storecredit_Model_Source_System_Config_Cms_Page extends Mage_Adminhtml_Model_System_Config_Source_Cms_Page
{
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array('0' => "-- Please Select --");
            $options = Mage::getResourceModel('cms/page_collection')
                ->load()->toOptionIdArray();
            $this->_options = array_merge($this->_options, $options);
        }
        return $this->_options;
    }
}