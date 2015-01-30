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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Activation_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm() 
	{
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl("*/*/activate"),
			'method' => 'post',
			'enctype' => 'multipart/form-data',
		));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		
		$client = Mage::helper('nscexport/data_client');
		$connectorInfo = $client->getConnectorInfoByCode($this->getCode());		
		if(!$connectorInfo)
		{
			$connectorInfo = $client->getConnectorInfoByCode();
		}
		$connectorInfo = new Varien_Object($connectorInfo);
		
		$fieldset = $form->addFieldset('activation_form', array(
			'legend' => Mage::helper('nscexport')->__('Activation Form')
		));		
		
		$fieldset->addField('code', 'hidden', array(
				'name' => 'code',
				'value' => $this->getCode(),
		));
		
		$fieldset->addField('name', 'text', array(
			'label' => $this->__('Name:'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'name'			
		));
		
		$fieldset->addField('email', 'text', array(
				'label' => $this->__('Email:'),
				'class' => 'required-entry validate-email',
				'required' => true,
				'name' => 'email'
		));
		
		if($connectorInfo->getIsTrial() == true)
		{
			$fieldset->addField('collection', 'select', array(
				'label' => $this->__('Feed Collection:'),
				'class' => 'required-entry',
				'required' => true,
				'name' => 'collection',
				'values' => $client->getAvailableCollectionsAsOptionArray(),
				'note' => $this->__('What is').' <a target="_blank" href="'.Mage::helper('nscexport')->getHelpUrl(Nostress_Nscexport_Helper_Data::HELP_FEED_COLLECTIONS).'">'
							.$this->__('Feed Collection').'</a>?'
			));
		}
		
		$licenseConditionsLink = $client->getHelpUrl(Nostress_Nscexport_Helper_Data::HELP_LICENSE_CONDITIONS);
		$fieldset->addField('accept_license_conditions', 'checkbox', array(
				'label' => $this->__('I accept Koongo License Condtiions'),
				'note' =>  $this->__('See').' <a href="'.$licenseConditionsLink.'" target="_blank">'.$this->__('Koongo License Condtions').'</a>',
				'name' => 'accept_license_conditions',
				'value' => 0,
				//'checked' => 'false',
				'onclick' => 'this.value = this.checked ? 1 : 0;',
				'disabled' => false,
				'readonly' => false,
				'required' => true,
		));
		
		$class = 'btn-koongo-submit-yellow';
		if($connectorInfo->getIsTrial() == true)
			$class = 'btn-koongo-submit-orange';
		
		$fieldset->addField('submit_and_activate', 'button', array(
			'label' => "",			
			'onclick'   => 'if(editForm.submit()){addOverlay();document.getElementById(\'loading-mask\').style.display = \'block\';}',			
			'class' => $class,
			'value' => strip_tags($connectorInfo->getLabel()),
		));
				
		return parent::_prepareForm();
	}
}