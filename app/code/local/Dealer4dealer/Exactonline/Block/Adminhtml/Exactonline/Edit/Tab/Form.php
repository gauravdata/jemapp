<?php
class Dealer4dealer_Exactonline_Block_Adminhtml_Exactonline_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		// Get the field and determine the field typer
		$fieldId = $this->getRequest()->getParam('id',0);

		if($fieldId>0) {
			$field = Mage::registry('exactonline_data');
			$settingKey = $field->getName();
			$keyDisabled = !(bool)$field->getIsEditableKey();
			$fieldType = $field->getFieldType();
		}else {
			$fieldType = 1;
			$keyDisabled = false;
		}


		// Get categories
		$categories = Mage::getModel('exactonline/category')
						->getCollection()
						->setOrder('sort_order','ASC')
						->addFieldToFilter('is_active',1);

		// Format array to be used in the form
		$categoryArray = $this->_prepareCategoryValues($categories);

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('exactonline_form', array('legend'=>Mage::helper('exactonline')->__('Item information')));

		$fieldset->addField('label', 'text', array(
			'name' => 'label',
			'class' => 'required-entry',
			'label' => Mage::helper('exactonline')->__('Label'),
			'required' => true,
		));

		$fieldset->addField('name', 'text', array(
			'label' => Mage::helper('exactonline')->__('Key'),
			'class' => 'required-entry',
			'required' => !$keyDisabled,
			'name' => 'name',
			'disabled'=> $keyDisabled
		));

		// Show the right field based on the type
		switch ($fieldType) {
			case 1:
				$fieldset->addField('value','text', array(
					'name' => 'value',
					'class' => 'required-entry',
					'label' => Mage::helper('exactonline')->__('Value'),
					'required' => true,
				));
				break;
			case 2:
				$fieldset->addField('value', 'select', array(
					'name' => 'value',
					'class' => 'required-entry',
					'label' => Mage::helper('exactonline')->__('Value'),
					'required' => true,
					'options'=>$this->_getSelectOptions($settingKey)
				));
				break;
			case 3:
				$fieldset->addField('value','password', array(
					'name' => 'value',
					'class' => 'required-entry',
					'label' => Mage::helper('exactonline')->__('Value'),
					'required' => true,
				));
				break;
			case 4:
				$fieldset->addField('value','textarea', array(
					'name' => 'value',
					'class' => '',
					'label' => Mage::helper('exactonline')->__('Value'),
					'required' => false,
				));
				break;
			case 5:
				$fieldset->addField('value','text', array(
					'name' => 'value',
					'class' => '',
					'label' => Mage::helper('exactonline')->__('Value'),
					'required' => false,
				));
				break;
			default:
				$fieldset->addField('value','text', array(
					'name' => 'value',
					'class' => 'required-entry',
					'label' => Mage::helper('exactonline')->__('Value'),
					'required' => true,
				));
				break;
		}

		$fieldset->addField('category_id', 'select', array(
			'name' => 'category_id',
			'class' => 'required-entry',
			'label' => Mage::helper('exactonline')->__('Category'),
			'required' => true,
			'options'=>$categoryArray
		));

		// Populate the form
		if ( Mage::getSingleton('adminhtml/session')->getExactonlineData() ){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getExactonlineData());
			Mage::getSingleton('adminhtml/session')->setExactonlineData(null);
		} elseif ( Mage::registry('exactonline_data') ) {
			$form->setValues(Mage::registry('exactonline_data')->getData());
		}
		return parent::_prepareForm();
	}

	/**
	 * Get the select options for the field
	 * and format them to use in the form.
	 *
	 * @param int $fieldId Id of the field
	 * @return array
	 */
	private function _getSelectOptions($fieldId)
	{
		$collection = Mage::getModel('exactonline/option')->getCollection()->addFieldToFilter('setting_key',$fieldId);

		$selectOptions = array();
		foreach($collection as $row) {
			$selectOptions[$row->getValue()] = Mage::helper('exactonline')->__($row->getLabel());
		}

		return $selectOptions;
	}

	/**
	 * Build an array of categories that can be
	 * used in the Varien_Data_Form.
	 *
	 * @param Dealer4dealer_Exactonline_Model_Category $categories
	 * @return array
	 */
	private function _prepareCategoryValues($categories)
	{
		$categoryArray = array();
		foreach($categories as $category) {
			$categoryArray[$category->getId()] = $category->getCategoryName();
		}

		return $categoryArray;
	}
}