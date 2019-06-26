<?php
class Biztech_Translator_Block_Adminhtml_Translator_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('translate_default_tabs');
        $this->setDestElementId('container-content');
        $this->setTitle(Mage::helper('translator')->__('Translation Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('search_string', array(
            'label' => Mage::helper('translator')->__('Search String &amp; Translate'),
            'title' => Mage::helper('translator')->__('Search String &amp; Translate'),
            'content' => $this->getLayout()->createBlock("translator/adminhtml_translator_form")->toHtml(),
        ));
        
       $this->addTab('categories_section', array(
                    'label'     => Mage::helper('translator')->__('Mass Category Translate'),
                    'title'     => Mage::helper('translator')->__('Mass Category Translate'),
                    'content'   => $this->getLayout()->createBlock('translator/adminhtml_translator_edit_tab_categories')->setTemplate('translator/categories.phtml')->toHtml(),
                ));
        return parent::_beforeToHtml();
    }
}