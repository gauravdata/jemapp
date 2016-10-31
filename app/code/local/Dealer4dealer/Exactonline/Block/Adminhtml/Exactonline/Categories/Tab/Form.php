<?php

class Dealer4dealer_Exactonline_Block_Adminhtml_Exactonline_Categories_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('exactonline_form', array('legend'=>Mage::helper('exactonline')->__('Item information')));
		$fieldset->addField('name', 'text', array(
			'label' => Mage::helper('exactonline')->__('Key'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'name',
		));
		$fieldset->addField('value', 'text', array(
			'name' => 'value',
			'class' => 'required-entry',
			'label' => Mage::helper('exactonline')->__('Value'),
			'required' => true,
		));
		if ( Mage::getSingleton('adminhtml/session')->getExactonlineData() ){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getExactonlineData());
			Mage::getSingleton('adminhtml/session')->setExactonlineData(null);
		} elseif ( Mage::registry('exactonline_data') ) {
			$form->setValues(Mage::registry('exactonline_data')->getData());
		}

		return parent::_prepareForm();
	}
}