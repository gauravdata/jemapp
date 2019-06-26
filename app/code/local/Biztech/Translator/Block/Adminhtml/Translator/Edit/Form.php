<?php

class Biztech_Translator_Block_Adminhtml_Translator_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

	public function __construct() {
		parent::__construct();
	}

	protected function _prepareForm() {

		$request = Mage::app()->getRequest();

		$store = Mage::app()->getStore($request->getParam('store'));

		$translateValues = Mage::helper('translator')->getTranslateRequestValues($request, $store);

		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/saveString'),
			'method' => 'post',
		));

		$this->setForm($form);
		$fieldset = $form->addFieldset('translate_form', array('legend' => Mage::helper('translator')->__('String Information')));

		$fieldset->addField('source_label', 'label', array(
			'label' => Mage::helper('translator')->__('Source: '),
			'class' => '',
			'name' => 'source',
		));

		$fieldset->addField('module', 'label', array(
			'label' => Mage::helper('translator')->__('Module: '),
			'class' => '',
			'name' => 'module',
		));

		$fieldset->addField('interface', 'label', array(
			'label' => Mage::helper('translator')->__('Interface: '),
			'class' => '',
			'name' => 'interface',
		));

		$fieldset->addField('store_name', 'label', array(
			'label' => Mage::helper('translator')->__('Store: '),
			'class' => '',
			'name' => 'store_name',
		));

		$fieldset->addField('original', 'label', array(
			'label' => Mage::helper('translator')->__('Original: '),
			'class' => '',
			'name' => 'module_original',
		));

		$fieldset->addField('string', (isset($translateValues['string']) && strlen($translateValues['string']) > 45 ? "textarea" : "text"), array(
			'label' => Mage::helper('translator')->__('String: '),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'string',
			'readonly' => true,
			'after_element_html' => '<p id="translate_error_msg"></p>',
		));

		$storeId = (int) $this->getRequest()->getParam('store', 0);
		$locales = array();
		if ($storeId == 0) {
			$localesOptions = Mage::getModel('translator/config_source_language')->getFormattedOptionArray();
		} else {

			$locale = Mage::getStoreConfig('general/locale/code', $storeId);
			array_push($locales, $locale);
			foreach (Mage::app()->getLocale()->getOptionLocales() as $key => $localeInfo) {
				if (in_array($localeInfo['value'], $locales)) {
					$localesOptions[$localeInfo['value']] = $localeInfo['label'];
				}
			}
		}

		$fieldset->addField('locale', 'select', array(
			'label' => Mage::helper('translator')->__('Locale: '),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'locale',
			'options' => $localesOptions,
			'after_element_html' => '<button id="translate_btn"'
			. 'class="scalable btn-translate" type="button" title="Translate" '
			. 'onclick="getAdminTranslation()">'
			. '<span><span><span>Translate</span></span></span>'
			. '</button>',
		));

		$fieldset->addField('storeid', 'hidden', array(
			'class' => '',
			'name' => 'storeid',
		));

		$fieldset->addField('original_translation', 'hidden', array(
			'class' => '',
			'name' => 'original_translation',
		));

		$fieldset->addField('source', 'hidden', array(
			'class' => '',
			'name' => 'source',
		));

		$fieldset->addField('translate_url', 'hidden', array(
			'class' => '',
			'name' => 'translate_url',
		));

		$form->setValues($translateValues);
		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}

}
