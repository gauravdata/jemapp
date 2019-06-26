<?php

/*
 * Overriden Core Adminhtml Catalog Product Block
 */

class Biztech_Translator_Block_Adminhtml_Catalog_Product_Edit extends Mage_Adminhtml_Block_Widget {

	public function __construct() {
		parent::__construct();
		$this->setTemplate('translator/edit.phtml');
		$this->setId('product_edit');
		// $this->_removeButton('saveandcontinue');
	}

	public function getBackButtonHtml() {
		return $this->getChildHtml('back_button');
	}

	public function getCancelButtonHtml() {
		return $this->getChildHtml('reset_button');
	}

	public function getSaveButtonHtml() {
		return $this->getChildHtml('save_button');
	}

	public function getSaveAndEditButtonHtml() {
		return $this->getChildHtml('save_and_edit_button');
	}

	public function getDeleteButtonHtml() {
		return $this->getChildHtml('delete_button');
	}

	public function getTranslateButtonHtml() {
		return $this->getChildHtml('translate_button');
	}

	public function getDuplicateButtonHtml() {
		return $this->getChildHtml('duplicate_button');
	}

	public function getValidationUrl() {
		return $this->getUrl('*/*/validate', array('_current' => true));
	}

	public function getSaveUrl() {
		return $this->getUrl('*/*/save', array('_current' => true, 'back' => null));
	}

	public function getProductId() {
		return $this->getProduct()->getId();
	}

	public function getProductSetId() {
		$setId = false;
		if (!($setId = $this->getProduct()->getAttributeSetId()) && $this->getRequest()) {
			$setId = $this->getRequest()->getParam('set', null);
		}
		return $setId;
	}

	public function getIsGrouped() {
		return $this->getProduct()->isGrouped();
	}

	public function getHeader() {
		$header = '';
		if ($this->getProduct()->getId()) {
			$header = $this->htmlEscape($this->getProduct()->getName());
		} else {
			$header = Mage::helper('catalog')->__('New Product');
		}
		if ($setName = $this->getAttributeSetName()) {
			$header .= ' (' . $setName . ')';
		}
		return $header;
	}

	public function getAttributeSetName() {
		if ($setId = $this->getProduct()->getAttributeSetId()) {
			$set = Mage::getModel('eav/entity_attribute_set')
				->load($setId);
			return $set->getAttributeSetName();
		}
		return '';
	}

	public function getIsConfigured() {
		if ($this->getProduct()->isConfigurable()
			&& !($superAttributes = $this->getProduct()->getTypeInstance(true)->getUsedProductAttributeIds($this->getProduct()))
		) {
			$superAttributes = false;
		}

		return !$this->getProduct()->isConfigurable() || $superAttributes !== false;
	}

	public function getSelectedTabId() {
		return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
	}

	public function getBiztechTranslatorConfiguration() {
		$config = array();
		$url = Mage::getUrl('adminhtml/translator/translate');
		$storeId = $this->getRequest()->getParam('store');
		$language = $this->getLanguage();
		$allLanguages = Mage::helper('translator/languages')->getLanguages();
		$fullFromCode = $this->getFromLanguage();
		$fullFromLanguageName = $this->getFromLangFullName();
		$translatedFields = Mage::getStoreConfig('translator/translator_general/massaction_translate_fields', $storeId);
		$translateBtnText = trim(Mage::getStoreConfig('translator/translator_general/translate_btntext', $storeId));
		$config['url'] = $url;
		$config['languageToFullName'] = $allLanguages[$language];
		$config['fullFromCode'] = $fullFromCode;
		$config['languageToCode'] = $language;
		$config['fullFromLanguageName'] = $fullFromLanguageName;
		$config['translatedFieldsNames'] = $translatedFields;
		$config['translateBtnText'] = $translateBtnText ? $translateBtnText : 'Translate To';
		return Mage::helper('core')->jsonEncode($config);

	}

	public function getLanguage() {
		$block = $this->getLayout()->getBlock('store_switcher');
		if ($block) {
			$storeId = $block->getStoreId();
			$language = Mage::helper('translator')->getLanguage($storeId);
		} else {
			$language = 'no-language';
		}

		return $language;
	}

	public function getFromLanguage() {
		$storeId = $this->getLayout()->getBlock('store_switcher')->getStoreId();
		$fromLanguage = Mage::helper('translator')->getFromLanguage($storeId);
		return $fromLanguage;
	}

	public function getFromLangFullName() {
		$storeId = $this->getLayout()->getBlock('store_switcher')->getStoreId();
		$language = $this->getFromLanguage();
		$allLanguages = Mage::helper('translator/languages')->getLanguages($storeId);
		if ($language) {
			return $allLanguages[$language];
		} else {
			return 'Auto detection';
		}

	}

	protected function _prepareLayout() {
		/*$trans = $this->getLayout()->createBlock('adminhtml/widget_button')
						->setData(array(
							'label' => Mage::helper('catalog')->__($translateBtnText . ' ' . $fullNameLanguage),
							'onclick' => 'javascript:translateproduct();',
							'before_html' => 'asd',
						))->toHtml();
*/

		if (Mage::helper('translator')->isEnable()) {
			$storeId = Mage::app()->getRequest()->getParam('store');
			$language = Mage::helper('translator')->getLanguage($storeId);
			$fullNameLanguage = Mage::helper('translator')->getLanguageFullNameByCode($language, $storeId);
			$translateBtnText = trim(Mage::getStoreConfig('translator/translator_general/translate_btntext', $storeId));
			$translateBtnText = $translateBtnText ? $translateBtnText : 'Translate To ';

			$this->setChild('translate_button',
				/*echo */$this->getLayout()->createBlock('adminhtml/widget_button')
					->setData(array(
						'label' => Mage::helper('catalog')->__($translateBtnText . ' ' . $fullNameLanguage),
						'onclick' => 'javascript:translateproduct();',

					)) /*->toHtml();*/
			);
		}

		if (!$this->getRequest()->getParam('popup')) {
			$this->setChild('back_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
					->setData(array(
						'label' => Mage::helper('catalog')->__('Back'),
						'onclick' => 'setLocation(\'' . $this->getUrl('*/*/', array('store' => $this->getRequest()->getParam('store', 0))) . '\')',
						'class' => 'back',
					))
			);
		} else {
			$this->setChild('back_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
					->setData(array(
						'label' => Mage::helper('catalog')->__('Close Window'),
						'onclick' => 'window.close()',
						'class' => 'cancel',
					))
			);
		}

		if (!$this->getProduct()->isReadonly()) {
			$this->setChild('reset_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
					->setData(array(
						'label' => Mage::helper('catalog')->__('Reset'),
						'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('_current' => true)) . '\')',
					))
			);

			$this->setChild('save_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
					->setData(array(
						'label' => Mage::helper('catalog')->__('Save'),
						'onclick' => 'productForm.submit()',
						'class' => 'save',
					))
			);
		}

		if (!$this->getRequest()->getParam('popup')) {
			if (!$this->getProduct()->isReadonly()) {
				$this->setChild('save_and_edit_button',
					$this->getLayout()->createBlock('adminhtml/widget_button')
						->setData(array(
							'label' => Mage::helper('catalog')->__('Save and Continue Edit'),
							'onclick' => 'saveAndContinueEdit(\'' . $this->getSaveAndContinueUrl() . '\')',
							'class' => 'save',
						))
				);
			}
			if ($this->getProduct()->isDeleteable()) {
				$this->setChild('delete_button',
					$this->getLayout()->createBlock('adminhtml/widget_button')
						->setData(array(
							'label' => Mage::helper('catalog')->__('Delete'),
							'onclick' => 'confirmSetLocation(\'' . Mage::helper('catalog')->__('Are you sure?') . '\', \'' . $this->getDeleteUrl() . '\')',
							'class' => 'delete',
						))
				);
			}

			if ($this->getProduct()->isDuplicable()) {
				$this->setChild('duplicate_button',
					$this->getLayout()->createBlock('adminhtml/widget_button')
						->setData(array(
							'label' => Mage::helper('catalog')->__('Duplicate'),
							'onclick' => 'setLocation(\'' . $this->getDuplicateUrl() . '\')',
							'class' => 'add',
						))
				);
			}

			/*if (Mage::helper('translator')->isEnable()) {
				$storeId = Mage::app()->getRequest()->getParam('store');
				$language = Mage::helper('translator')->getLanguage($storeId);
				$fullNameLanguage = Mage::helper('translator')->getLanguageFullNameByCode($language, $storeId);
				$translateBtnText = trim(Mage::getStoreConfig('translator/translator_general/translate_btntext', $storeId));
				$translateBtnText = $translateBtnText ? $translateBtnText : 'Translate To ';

				$this->setChild('translate_button',
					$this->getLayout()->createBlock('adminhtml/widget_button')
						->setData(array(
							'label' => Mage::helper('catalog')->__($translateBtnText . ' ' . $fullNameLanguage),
							'onclick' => 'javascript:translateproduct();',
							'before_html' => 'asd',
						))
				);
			}*/
		}

		return parent::_prepareLayout();
	}

	/**
	 * Retrieve currently edited product object
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	public function getProduct() {
		return Mage::registry('current_product');
	}

	public function getSaveAndContinueUrl() {
		return $this->getUrl('*/*/save', array(
			'_current' => true,
			'back' => 'edit',
			'tab' => '{{tab_id}}',
			'active_tab' => null,
		));
	}

	public function getDeleteUrl() {
		return $this->getUrl('*/*/delete', array('_current' => true));
	}

	public function getDuplicateUrl() {
		return $this->getUrl('*/*/duplicate', array('_current' => true));
	}
}