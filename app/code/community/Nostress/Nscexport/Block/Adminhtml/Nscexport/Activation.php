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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Activation extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$this->_objectId = 'id';
		$this->_controller = 'adminhtml_nscexport';
		$this->_blockGroup = 'nscexport';
		$this->_mode = 'activation';
		
		parent::__construct();
		
		$this->addLiveChatButton();
		$this->_removeButton('reset');
		$this->_removeButton('save');
		$this->_removeButton('back');
	}
	
	protected function addLiveChatButton() {
	
		$this->_addButton('livechat', $this->helper('nscexport')->getLivechatButtonOptions(), -1);
	}
	
	public function getHeaderText()
	{
		$client = Mage::helper('nscexport/data_client');
		$connectorInfo = $client->getConnectorInfoByCode($this->getCode());
		if(empty($connectorInfo))
			$connectorInfo = $client->getConnectorInfoByCode();
		
		$title = "";
		if(isset($connectorInfo["title"]))
			$title = $connectorInfo["title"];
		return Mage::helper('nscexport')->__('Koongo Connector')." - ".$title;//." ".Mage::helper('nscexport')->__('Activation');
	}
}