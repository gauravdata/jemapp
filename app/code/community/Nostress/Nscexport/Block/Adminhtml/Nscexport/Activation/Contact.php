<?php
/** 
* Magento Module developed by NoStress Commerce 
* 
* NOTICE OF LICENSE 
* 
* This source file is subject to the Open Software License (OSL 3.0) 
* that is bundled with this package in the file LICENSE.txt. 
* It is also available through the world-wide-web at this URL: 
* http://opensource.org/licenses/osl-3.0.php 
* If you did of the license and are unable to 
* obtain it through the world-wide-web, please send an email 
* to info@nostresscommerce.cz so we can send you a copy immediately. 
* 
* @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* @category Nostress 
* @package Nostress_Nscexport
*/

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Activation_Contact extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm() 
	{
		$client = Mage::helper('nscexport/data_client');
		$connectorInfo = $client->getConnectorInfoByCode($this->getCode());		
		if(!$connectorInfo)
		{
			$connectorInfo = $client->getConnectorInfoByCode();
		}
		$connectorInfo = new Varien_Object($connectorInfo);
		
		$form = new Varien_Data_Form(array(
			'id' => 'contact_form',
			'action' => $this->getUrl("*/*/post"),
			'method' => 'post',
			'enctype' => 'multipart/form-data',
		));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('contact_form', array(
			'legend' => Mage::helper('nscexport')->__('Contact Form')
		));		
		
		$fieldset->addField('code', 'hidden', array(
				'name' => 'code',
				'value' => $this->getCode(),
		));
		
		$fieldset->addField('subject_field', 'text', array(
			'label' => $this->__('Subject:'),
			'disabled' => 'disabled',
			'name' => 'subject_field',
			'value' => $this->__('Koongo Trial activation request')			
		));
		
		$fieldset->addField('subject', 'hidden', array(
				'label' => $this->__('Subject:'),				
				'name' => 'subject',
				'value' => $this->__('Koongo Trial activation request')
		));
		
		$fieldset->addField('from_email', 'text', array(
				'label' => $this->__('Email:'),
				'class' => 'required-entry validate-email',
				'required' => true,
				'name' => 'from_email'		
		));
		
		$serverId = Mage::helper('nscexport/version')->getServerId();
		$bodyText = $this->__('Hi,'.PHP_EOL.
								'I need help with Koongo Trial activation.'.PHP_EOL.								
								'My backend URL is: %s'.PHP_EOL.
								'Server Id: %s'.PHP_EOL.
								'Backend username:'.PHP_EOL.
								'Password:'.PHP_EOL.PHP_EOL.											
								
								'Thanks, '.PHP_EOL,Mage::helper('adminhtml')->getUrl('adminhtml'),$serverId);
		
		$fieldset->addField('body', 'textarea', array(
				'label' => $this->__('Question:'),
				'name' => 'body',
				'class' => 'required-entry',
				'required' => true,
				'value' => $bodyText
		));
		
		$class = 'btn-koongo-submit-yellow';
		if($connectorInfo->getIsTrial() == true)
			$class = 'btn-koongo-submit-orange';
		
		$fieldset->addField('send', 'button', array(	
			'value' => $this->__('Send Email'),
			'onclick'   => 'contactForm.submit();',
			'class' => $class,
		));
				
		return parent::_prepareForm();
	}
}