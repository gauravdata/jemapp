<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Block_Adminhtml_Translations_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        ));
        
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('translationhelper')->__('General'),
            'class' => 'fieldset-wide' 
        ));
        
        $fieldset->addField('string', 'text', array(
            'name' => 'string',
            'label' => 'String',
            'required' => true,
            'class' => 'required-entry'
        ));
        
        $fieldset->addField('translate', 'text', array(
            'name' => 'translate',
            'label' => 'Translation',
            'required' => true,
            'class' => 'required-entry'
        ));
        
        $options = Mage::helper('translationhelper')->getStores();
        $options[0] = 'Any';
        ksort($options);
        
        $fieldset->addField('store_id', 'select', array(
            'name' => 'store_id',
            'label' => 'Store',
            'values' => $options
        ));
        
        $options = Mage::helper('translationhelper')->getLocales();
        
        $fieldset->addField('locale', 'select', array(
            'name' => 'locale',
            'label' => 'Locale',
            'values' => $options
        ));
        
        $fieldset->addField('is_hidden', 'select', array(
            'name' => 'is_hidden',
            'label' => 'Hidden',
            'values' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ));
        
        $fieldset->addField('is_imported', 'hidden', array(
            'name' => 'is_imported'
        ));
        
        $fieldset->addField('is_missing', 'hidden', array(
            'name' => 'is_missing'
        ));
        
        if (Mage::registry('translation_data')) {
            $form->setValues(Mage::registry('translation_data')->getData());
            $form->getElement('is_imported')->setValue(0);
            $form->getElement('is_missing')->setValue(0);
        }
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}