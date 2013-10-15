<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Block_Adminhtml_Translations_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('translationsGrid');
        $this->setDefaultSort('key_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        //$collection = Mage::getResourceSingleton('core/translate')->getCollection();
        $collection = Mage::getModel('translationhelper/translation')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $localeOptions = Mage::helper('translationhelper')->getLocales();
        $storeOptions = Mage::helper('translationhelper')->getStores();
        $this->addColumn('key_id', array(
            'header' => 'ID',
            'align' => 'left',
            'index' => 'key_id'
        ))->addColumn('string', array(
            'header' => 'String',
            'align' => 'left',
            'index' => 'string'
        ))->addColumn('translate', array(
            'header' => 'Translate',
            'align' => 'left',
            'index' => 'translate'
        ))->addColumn('store_id', array(
            'header' => 'Store',
            'align' => 'left',
            'index' => 'store_id',
            'type' => 'options',
            'options' => $storeOptions,
            'renderer' => 'Twm_TranslationHelper_Block_Adminhtml_Translations_Renderer'
        ))->addColumn('locale', array(
            'header' => 'Locale',
            'align' => 'left',
            'index' => 'locale',
            'type' => 'options',
            'options' => $localeOptions,
            'renderer' => 'Twm_TranslationHelper_Block_Adminhtml_Translations_Renderer'
        ))->addColumn('is_imported', array(
            'header' => 'Imported',
            'align' => 'left',
            'index' => 'is_imported',
            'type' => 'options',
            'options' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ))->addColumn('is_missing', array(
            'header' => 'Missing',
            'align' => 'left',
            'index' => 'is_missing',
            'type' => 'options',
            'options' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ))->addColumn('is_hidden', array(
            'header' => 'Hidden',
            'align' => 'left',
            'index' => 'is_hidden',
            'type' => 'options',
            'options' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ));

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction() {
        $this->setMassactionIdField('key_id');
        $this->getMassactionBlock()->setFormFieldName('key_id');
        $this->getMassactionBlock()->addItem('hide', array(
            'label' => Mage::helper('translationhelper')->__('Hide'),
            'url' => $this->getUrl('*/*/massHide', array('' => '')),
            'confirm' => Mage::helper('translationhelper')->__('Are you sure?')
        ))->addItem('unhide', array(
            'label' => Mage::helper('translationhelper')->__('Unhide'),
            'url' => $this->getUrl('*/*/massUnhide', array('' => '')),
            'confirm' => Mage::helper('translationhelper')->__('Are you sure?')
        ))->addItem('delete', array(
            'label' => Mage::helper('translationhelper')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => Mage::helper('translationhelper')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}