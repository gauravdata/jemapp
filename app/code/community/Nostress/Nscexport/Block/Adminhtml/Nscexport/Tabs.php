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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	
	protected $_profile;
	
	/**
	* Default Attribute Tab Block
	*
	* @var string
	*/
	protected $_attributeTabBlock = 'nscexport/adminhtml_nscexport_tabs';
	
	/**
	* Initialize Tabs
	*
	*/
	public function __construct() {
		parent::__construct();
		$this->setId('profile_info_tabs');
		$this->setDestElementId('profile_tab_content');
		$this->setTitle(Mage::helper('catalog')->__('Category Data'));
		$this->setTemplate('widget/tabshoriz.phtml');
	}
	
	/**
	* Retrieve profile object
	*
	* @return Nostress_Nscexport_Model_Profile
	*/
	public function getProfile() {
		if (!$this->_profile) {
			$this->_profile = Mage::registry('nscexport_profile');
		}
		return $this->_profile;
	}
	
	/**
	* Return Adminhtml Catalog Helper
	*
	* @return Mage_Adminhtml_Helper_Catalog
	*/
	public function getProfileHelper() {
		return Mage::helper('adminhtml/catalog');
	}
	
	/**
	* Getting attribute block name for tabs
	*
	* @return string
	*/
	public function getAttributeTabBlock() {
		if ($block = $this->getProfileHelper()->getCategoryAttributeTabBlock()) {
			return $block;
		}
		return $this->_attributeTabBlock;
	}
	
	/**
	* Prepare Layout Content
	*
	* @return Nostress_Nscexport_Block_Adminhtml_Nscexport_Tabs
	*/
	protected function _prepareLayout() {
		$config = "";
		$feed = "";
		if (!$this->getProfile()->getId()) {
			$feed = $this->getProfile()->getFeed();
			
			if ($feed) {
				$config = Mage::getModel('nscexport/profile')->getBackendConfig($feed);
			}
		}
		else {
			$config = $this->getProfile()->getBackendConfig();
		}
		
		if(Mage::registry('nscexport_profile_config'))
			Mage::unregister('nscexport_profile_config');
		Mage::register('nscexport_profile_config', $config);
		
		$this->addTab('general', array(
			'label' => Mage::helper('nscexport')->__('General'),
			'content' => $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_tab_general')
				->setData(array(
					"config1" => $config
				))
				->toHtml(),
			'active' => true
		));
		
		$this->addTab('feed', array(
			'label' => Mage::helper('nscexport')->__('Feed details'),
			'content' => $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_tab_feed')->toHtml(),
			'active' => false
		));
		
		$this->addTab('products', array(
			'label' => Mage::helper('nscexport')->__('Product filter'),
			'content' => $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_tab_products')->toHtml(),
			'active' => false
		));
		
        $this->addTab('productscat', array(
            'label'     => Mage::helper('catalog')->__('Attribute filter'),
            'content' => $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_tab_attributes')->toHtml(),
        	'active'  => false
        ));
        
        $this->addTab('ftp', array(
                'label'     => Mage::helper('catalog')->__('FTP Settings'),
                'content' => $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_tab_ftp')->toHtml(),
                'active'  => false
        ));

		return parent::_prepareLayout();
	}
}