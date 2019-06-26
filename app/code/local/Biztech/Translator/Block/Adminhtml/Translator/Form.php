<?php

class Biztech_Translator_Block_Adminhtml_Translator_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareForm() {
        
        $form = new Varien_Data_Form(
                        array('id' => 'search_translate_form',
                            'action' => $this->getUrl('*/*/translateSearch', array('id' => $this->getRequest()->getParam('id'))),
                            'method' => 'post')
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('translator_form', array('legend' => Mage::helper('translator')->__('Search String &amp; Translate')));

        $fieldset->addField('searchString', 'text', array(
            'label' => Mage::helper('translator')->__('Search string'),
            'name' => 'searchString',
            'after_element_html' => '<p class="note">' . Mage::helper('translator')->__('Enter a string (e.g. "Customers")') . '</p>'
        ));

       
        $fieldset->addField('locale', 'select', array(
            'label' => Mage::helper('translator')->__('Locale'),
            'name' => 'locale',
            'options' => Mage::getModel('translator/system_config_locales')->toOptionArray(),
            'after_element_html' => '<p class="note">' . Mage::helper('translator')->__('Locale of all Stores.') . '</p>'
        ));
        
      
        $fieldset->addField('modules', 'select', array(
            'label' => Mage::helper('translator')->__('Modules'),
            'name' => 'modules',
            'values' => Mage::getModel('translator/system_config_magemodules')->toOptionArray(),
            'after_element_html' => '<p class="note">' . Mage::helper('translator')->__('List of all Modules.') . '</p>'
        ));

       
        $fieldset->addField('interface', 'select', array(
            'label' => Mage::helper('translator')->__('Interface'),
            'name' => 'interface',
            'values' => Mage::getModel('translator/system_config_magemodules')->getInterfaceArray(),
        ));
        

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                    'label' => Mage::helper('translator')->__('Search'),
                    'class' => 'save',
                    'onclick' => 'matchSearchString(\'' . $this->getUrl('*/*/translateSearch') . '\')',
                    'id' => 'form_search_submit'
                   ));
        
        
        $buttonReset = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                        'label' => Mage::helper('translator')->__('Reset'),
                        'class' => 'back',
                        'onclick' => 'translateSearchReset()',
                       ));

        $fieldset->addField('submit', 'note', array(
            'label' => '',
            'class' => 'button',
            'required' => false,
            'name' => 'submit',
            'text' => $buttonReset->toHtml() . ' ' . $button->toHtml(),
        ));
        
        $resultField = $form->addFieldset('searchResult', array('legend' => Mage::helper('translator')->__('Search Results :')));
       
        return parent::_prepareForm();
    }

}