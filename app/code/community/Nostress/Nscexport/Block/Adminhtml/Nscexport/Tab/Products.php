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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab_Products extends Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab
{	
	public function __construct() {
		parent::__construct();
		$this->setShowGlobalIcon(true);
	}		
	
	public function _prepareLayout() {
		parent::_prepareLayout();
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('_products');		
		$yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
		
		$fieldset = $form->addFieldset('common_fieldset', array('legend' => Mage::helper('nscexport')->__('Common Filter')));
		$fieldset->setHeaderBar($this->getHelpButtonHtmlByFieldset("product_common"));
		$fieldset->addType( 'nscexport_checkboxes', 'Nostress_Nscexport_Model_Data_Form_Element_Checkboxes');
		
		
		$a = Mage_Catalog_Model_Product_Type::getOptionArray();
		$fieldset->addField( 'types', 'nscexport_checkboxes', array(
		    'name'        => 'types[]',
			'label'       => Mage::helper('nscexport')->__('Product Types:'),
			'values'      => Mage_Catalog_Model_Product_Type::getOptions(),
			'value' => array_keys(Mage_Catalog_Model_Product_Type::getOptionArray())
		));
		
		$fieldset->addField('parents_childs', 'select', array(
			'label' => Mage::helper('nscexport')->__("Parent - Child:"),
			'name' => "parents_childs",
			'values' => Mage::getSingleton('nscexport/config_source_parentschilds')->toOptionArray(),
			
		));			
		
		$fieldset->addField('use_product_filter', 'select', array(
			'label' => Mage::helper('nscexport')->__("Use Category-Product Filter:"),
			'name' => "use_product_filter",
			'onchange' => "showHideCategoryProductFilter(this);",
			'values' => $yesnoSource
		));
		
		$fieldset2 = $form->addFieldset('attributes_fieldset', array('legend' => Mage::helper('nscexport')->__('Category - Product Filter')));
		$fieldset2->setHeaderBar($this->getHelpButtonHtmlByFieldset("product_cp"));
		$renderer = $this->getLayout()->createBlock('nscexport/adminhtml_nscexport_tab_products_renderer_fieldset');
		$fieldset2->setRenderer($renderer);
		$fieldset2->addType('categories','Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Categories');
		
		$fieldset2->addField('nscexport_categories_filter', 'categories', array(
			//'name' => 'categories[]',
			//'label' => Mage::helper('nscexport')->__('Categories Filter:'),
			'values' => array(
				"categoryRoot" => Mage::app()->getStore($this->getProfile()->getStoreId())->getRootCategoryId(), 
				"store" => Mage::app()->getStore($this->getProfile()->getStoreId())->getId(),
				"profile_id" => $this->getRequest()->getParam('id')
				)
		), 'frontend_class');
		//echo "<pre>".print_r(Mage::app()->getStore($this->getProfile()->getStoreId())->getRootCategoryId(), 1)."</pre>";
		$form->getElement('types')->setSize(6);		
		$form->addValues($this->getProductConfig());
		$form->setFieldNameSuffix('product');
		$this->setForm($form);
	}
	
	/**
	* Retrieve additional element types for product attributes
	*
	* @return array
	*/
	protected function _getAdditionalElementTypes() {
		return array(
			'apply' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_apply'),
		);
	}
}
