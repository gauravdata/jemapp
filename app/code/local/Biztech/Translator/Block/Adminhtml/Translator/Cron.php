<?php

class Biztech_Translator_Block_Adminhtml_Translator_Cron extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_objectId = 'cron_id';
        $this->_blockGroup = 'translator';
        $this->_controller = 'adminhtml_translator_cron';
        $this->_headerText = Mage::helper('translator')->__('Cron Translation Module');
        // $this->setTemplate('translator/container.phtml');
        parent::__construct();
        $this->_removeButton('add');
        $this->removebutton('edit');
        $this->removebutton('back');
    }

    /*public function getHeaderText()
    {
        return Mage::helper('translator')->__("Cron Translation Module");
    }*/
}

