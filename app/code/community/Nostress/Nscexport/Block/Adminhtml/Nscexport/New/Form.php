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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_New_Form extends Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab
{
	protected function _prepareForm() {
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl("*/*/edit", array('id' => $this->getRequest()->getParam('id'))),
			'method' => 'post',
			'enctype' => 'multipart/form-data',
		));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('edit_form', array(
			'legend' =>Mage::helper('nscexport')->__('Select Store View and Feed')
		));
		$fieldset->setHeaderBar($this->getHelpButtonHtmlByFieldset("step1"));
		
		$fieldset->addField('profile_store_id', 'select', array(
			'label' => Mage::helper('nscexport')->__('Store View:'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'store',
			'note' => Mage::helper('nscexport')->__('Select Store View.'),
			'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false)
		));
		
		
		if(Mage::helper('nscexport/data_feed')->someFeedsDisabled())
		{	
			$title = Mage::helper('nscexport')->__("Are you missing a feed? Some of the feeds are disabled in connector configuration.");
			$fieldset->addField('nscexport_feed_note', 'link', array(
					'title' => $title,
					'value' => $title,
					'href' => Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit/section/koongo_config'),	
					'target' => "_blank"						
			));
		}
		
		$fieldset->addField('nscexport_feed', 'select', array(
			'label' => Mage::helper('nscexport')->__('Feed:'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'feed',
			'note' => Mage::helper('nscexport')->__('Select Feed.'),
			'onchange' => "showOption(this); changeType(this)",
			'values' => Mage::helper('nscexport/data_feed')->getFeedOptions(true, false, false)
		));
		
		$fieldset->addField('nscexport_type', 'select', array(
			'label' => Mage::helper('nscexport')->__('Type'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'type',
			'onchange' => "changeFile(this)",
			'values' => array()
		));
		
		$fieldset->addField('nscexport_file', 'select', array(
			'label' => Mage::helper('nscexport')->__('File'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'file',
			'values' => array()
		));
		
		/*$fieldset->addField('next', 'button', array(
			'label' => "",
			'value' => "Next Step"
		));*/
		
		return parent::_prepareForm();
	}
}