<?php

/**
 * @category    Twm
 * @package     Twm_TranslationHelper
 * @author      The Webmen <info@thewebmen.com>
 * @copyright   2012 The Webmen
 */
class Twm_TranslationHelper_Block_Adminhtml_Translations_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_blockGroup = 'translationhelper';

        $this->_controller = 'adminhtml_translations';

        $this->_updateButton('save', 'label', 'Save Translation');
        $this->_updateButton('delete', 'label', 'Delete Translation');
		
		$model = Mage::registry('translation_data');
        if (isset($model)) {
			$this->addButton('duplicate', array(
				'label' => 'Duplicate Translation',
				'class' => 'add',
				'onclick' => 'setLocation(\'' . $this->getUrl('*/*/duplicate/duplicate_id/' . $model->getId()) . '\');'
			));
		}
    }
    
    public function getHeaderText() {
        if (Mage::registry('translation_data') && Mage::registry('translation_data')->getId()) {
            return 'Edit translation';
        } else {
            return 'Add translation';
        }
    }
}