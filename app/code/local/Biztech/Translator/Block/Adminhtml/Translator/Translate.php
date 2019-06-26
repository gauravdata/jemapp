<?php
class Biztech_Translator_Block_Adminhtml_Translator_Translate extends Mage_Adminhtml_Block_Widget_View_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId   = 'test_id';
        $this->_blockGroup = 'translator';
        $this->_controller = 'adminhtml_translator';
        $this->setTemplate('translator/container.phtml');
        $this->removebutton('edit');
        $this->removebutton('back');
    }
    
    public function getHeaderText() {
        return Mage::helper('translator')->__("Translation module");
    }
}
