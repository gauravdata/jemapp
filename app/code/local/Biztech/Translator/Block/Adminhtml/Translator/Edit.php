<?php

class Biztech_Translator_Block_Adminhtml_Translator_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'translator';
        $this->_controller = 'adminhtml_translator';
        $this->_mode = 'edit';    

        $this->_updateButton('save', 'label', Mage::helper('translator')->__('Save Translation'));
        $this->_removeButton('delete');
       
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('translate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'translate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'translate_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        return Mage::helper('translator')->__('Edit Translation String');
    }

}