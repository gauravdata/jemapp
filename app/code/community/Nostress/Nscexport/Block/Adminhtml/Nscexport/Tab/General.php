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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab_General extends Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab
{
	public function __construct() {
		parent::__construct();
		$this->setShowGlobalIcon(true);
	}
	
	public function _prepareLayout() {
		parent::_prepareLayout();
				
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('_general');
		$fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('nscexport')->__('General Information'), 'class' => "collapseable"));
		
		$fieldset->setHeaderBar($this->getHelpButtonHtmlByFieldset("general_info"));
		$yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
				$compressFile = "";
				
		$generalConfig = $this->getGeneralConfig();
						
		if ($this->getProfile()->getId()) {
			$fieldset->addField('id', 'hidden', array(
				'name' => 'id',
				'value' => $this->getProfile()->getId()
			));
		}
		
		$freqItem = new Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency();
      	$freqArr = $freqItem->toOptionArray();
		
      	$fieldset->addField('feed', 'hidden', array(
				'name' => 'feed',
				'value' => $this->getProfile()->getFeed()
			));
		
		$fieldset->addField('store_id', 'hidden', array(
			'name' => 'store_id',
			'value' => $this->getProfile()->getStoreId()
		));
		
		$fieldset->addField('store', 'text', array(
			'label' => Mage::helper('nscexport')->__("Store View:"),
			'name' => "store",
			'disabled' => true,
			'value' => $this->getStoreName($this->getProfile()->getStoreId())
		));
		
		$enabled = $this->getProfile()->getEnebled();
		$fieldset->addField('enabled', 'select', array(
			'label' => Mage::helper('nscexport')->__("Enabled:"),
			'name' => "enabled",
			'values' => $yesnoSource,
			'value' => !empty($enabled)?$enabled:"1"
		));
		
		$fieldset->addField('name', 'text', array(
			'label' => Mage::helper('nscexport')->__("Profile Name:"),
			'name' => "name",
			'required' => true
		));
		
		$fieldset->addField('nscexport_filename', 'text', array(
			'label' => Mage::helper('nscexport')->__("Export File Name:"),
			'name' => "filename",
			'required' => true,
			'note' => Mage::helper('nscexport')->__("File Name without suffix - . xml or .csv will be added according to selected feed type."),
			'value' => $this->getProfile()->getFilename(false,"")
		));
		
		$fieldset->addField('compress_file', 'select', array(
			'label' => Mage::helper('nscexport')->__("Compress File:"),
			'name' => "compress_file",
			'values' => $yesnoSource
		));
		
		$fieldset2 = $form->addFieldset('cron_fieldset', array('legend' => Mage::helper('nscexport')->__('Cron Schedule'), 'class' => "collapseable"));
		
		$fieldset2->setHeaderBar($this->getHelpButtonHtmlByFieldset("general_info"));
		$fieldset2->addType( 'nscexport_checkboxes', 'Nostress_Nscexport_Model_Data_Form_Element_Checkboxes');
			
		$field = $fieldset2->addField( 'cron_days', 'nscexport_checkboxes', array(
	        'label' => Mage::helper('nscexport')->__("Days").":",
	        'name' => "cron_days[]",
	        'values' => Mage::getSingleton('nscexport/config_source_dayofweek')->toOptionArray()
		));
		
		$field = $fieldset2->addField( 'cron_times', 'nscexport_checkboxes', array(
	        'label' => Mage::helper('nscexport')->__("Times").":",
	        'name' => "cron_times[]",
	        'values' => Mage::getSingleton('nscexport/config_source_daytimes')->toOptionArray(),
		    'class' => 'floated'
		));
		
		if(isset($generalConfig["compress_file"]))
		{
			$form->addValues(array("compress_file" => $generalConfig["compress_file"]));
		}

		$id = $this->getProfile()->getId();
		if($id)
		{
			$cronModel = Mage::getModel("nscexport/cron");
			$generalConfig["cron_days"] = $cronModel->getDaysPerProfile($id);
			$generalConfig["cron_times"] = $cronModel->getTimesPerProfile($id);
		}
		else
		{
			$generalConfig["cron_days"] = Mage::getSingleton('nscexport/config_source_dayofweek')->getAllValues();
			$generalConfig["cron_times"] = array(Mage::getSingleton('nscexport/config_source_daytimes')->getDefaultValue());
		}				 
		
		$form->addValues($this->getProfile()->getData());		
		// vlozeni dnu a casu do formulare
		$form->addValues( $generalConfig);
		
		$form->setFieldNameSuffix('general');
		$this->setForm($form);
	}
}
