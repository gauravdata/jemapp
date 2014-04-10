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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab_Ftp extends Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab
{
	public function __construct() {
		parent::__construct();
		$this->setShowGlobalIcon(true);
	}
	
	public function _prepareLayout() {
		parent::_prepareLayout();
				
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix(Nostress_Nscexport_Model_Profile::UPLOAD."_");
		$fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('nscexport')->__('FTP settings'), 'class' => "collapseable"));
		
		$fieldset->setHeaderBar($this->getHelpButtonHtmlByFieldset("ftp_info"));
		$yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

		$ftpConfig = $this->getFtpConfig();
		
		$fieldset->addType('nostress_button','Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Button');
				
		$fieldset->addField('enabled', 'select', array(
	        'label' => Mage::helper('nscexport')->__("Enabled:"),
	        'name' => "enabled",
	        'values' => $yesnoSource,
		));
		
		$fieldset->addField('hostname', 'text', array(
	        'label' => Mage::helper('nscexport')->__("Host Name:"),
	        'name' => "hostname",
		    'note' => 'e.g. "ftp.domain.com"'
		));
		
		$fieldset->addField('port', 'text', array(
	        'label' => Mage::helper('nscexport')->__("Port:"),
	        'name' => "port",
		    'value' => 21
		));
		
		$fieldset->addField('username', 'text', array(
	        'label' => Mage::helper('nscexport')->__("User Name:"),
	        'name' => "username",
		));
		
		$fieldset->addField('password', 'password', array(
	        'label' => Mage::helper('nscexport')->__("Password:"),
	        'name' => "password",
		));
		
		$fieldset->addField('path', 'text', array(
	        'label' => Mage::helper('nscexport')->__("Path:"),
	        'name' => "path",
	        'value' => '/',
	        'note' => 'e.g. "/yourfolder"'
		));
		
		$fieldset->addField('passive_mode', 'select', array(
	        'label' => Mage::helper('nscexport')->__("Passive Mode:"),
	        'name' => 'passive_mode',
	        'values' => $yesnoSource
		));
		
		$urlTestFtpConnection = $this->getUrl("*/*/testFtpConnection");
		$fieldset->addField('test_connection', 'nostress_button', array(
	        'label' => Mage::helper('nscexport')->__("Test Connection"),
	        'name' => 'test_connection',
		    'class' => 'save',
		    'onclick' => "testFtpConnection('$urlTestFtpConnection');"
		));
		
 		$form->addValues($this->getUploadConfig());
		
		$form->setFieldNameSuffix(Nostress_Nscexport_Model_Profile::UPLOAD);
		$this->setForm($form);
	}
}
