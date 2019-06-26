<?php

    class Biztech_Translator_Block_Adminhtml_Translator_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

        public function __construct() {
            parent::__construct();
            $this->setId('translate_tabs');
            $this->setDestElementId('edit_form');
            $this->setTitle(Mage::helper('translator')->__('String Information'));
        }

        protected function _beforeToHtml() {
            $this->addTab('form_section', array(
                    'label' => Mage::helper('translator')->__('String Information'),
                    'title' => Mage::helper('translator')->__('String Information'),
                    'content' => $this->getLayout()->createBlock('translator/adminhtml_translator_edit_form')->toHtml(),
                ));
           


            return parent::_beforeToHtml();
        }

}