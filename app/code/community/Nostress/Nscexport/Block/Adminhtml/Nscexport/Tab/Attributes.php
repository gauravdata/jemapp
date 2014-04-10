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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab_Attributes extends Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab
{
	public function __construct() {
		parent::__construct();
		$this->setShowGlobalIcon(true);
	}
	
	public function _prepareLayout() {
	    
		parent::_prepareLayout();
		
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('attributes_');
		$yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
		
		$fieldsetStock = $form->addFieldset('stock_fieldset', array('legend' => Mage::helper('nscexport')->__('Stock Filter')));
		$fieldsetStock->setHeaderBar($this->getHelpButtonHtmlByFieldset("attribute_common"));
		
		$stock = $this->getAttributeValue("common", "stock");
		
		$fieldsetStock->addField('stock_status_dependence', 'select', array(
				'label' => Mage::helper('nscexport')->__("Stock status dependence").":",
				'name' => "stock_status_dependence",
				'values' => Mage::getSingleton('nscexport/config_source_stockdependence')->toOptionArray(),
				//load value from previous stored field
				'value' => $this->arrayField($stock,"stock_status_dependence",Nostress_Nscexport_Model_Config_Source_Stockdependence::STOCK_AND_QTY)
		));
		
		$fieldsetStock->addField('export_out_of_stock', 'select', array(
				'label' => Mage::helper('nscexport')->__("Export Out of Stock Products").":",
				'name' => "export_out_of_stock",
				'values' => $yesnoSource
		));
		
		$visibilityFieldset = $form->addFieldset('common_fieldset', array('legend' => Mage::helper('nscexport')->__('Visibility Filter')));
		$visibilityFieldset->setHeaderBar($this->getHelpButtonHtmlByFieldset("attribute_visibility"));
		$visibilityFieldset->addType('apply','Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Apply');
		$visibilityFieldset->addType( 'nscexport_checkboxes', 'Nostress_Nscexport_Model_Data_Form_Element_Checkboxes');
							
		$field = $visibilityFieldset->addField( Nostress_Nscexport_Model_Profile::VISIBILITY, 'nscexport_checkboxes', array(
	        'label' => Mage::helper('nscexport')->__("General Product Visiblity").":",
	        'name' => Nostress_Nscexport_Model_Profile::VISIBILITY."[]",
	        'values' => Mage::getSingleton('nscexport/visibility')->getAllOptions( false),
		    'value' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
		));
		$field->setSize( 4);
		
		$field = $visibilityFieldset->addField(Nostress_Nscexport_Model_Profile::VISIBILITY_PARENT, 'nscexport_checkboxes', array(
	        'label' => Mage::helper('nscexport')->__("Parent Product Visibility").":",
	        'name' => Nostress_Nscexport_Model_Profile::VISIBILITY_PARENT."[]",
	        'values' => Mage::getSingleton('nscexport/visibility')->getAllOptions( false),
		    'value' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
		));
		$field->setSize( 4);
		
		$model = $this->getProfile();
		
		$renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
		    ->setTemplate('nscexport/tabs/attributes/fieldset.phtml')
		    ->setNewChildUrl($this->getUrl('adminhtml/nscexport_action/newConditionHtml', array('form'=>'conditions','feed_code' => $this->getProfile()->getFeed(), 'store'=>$this->getProfile()->getStoreId())))
		    ;

		$fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('nscexport')->__('Attribute filter (leave blank for all products)'))
        )->setRenderer($renderer);
		$fieldset->setHeaderBar($this->getHelpButtonHtmlByFieldset("attribute_conditions"));

		if(Mage::registry('nsc_current_feed_code'))
		 	Mage::unregister('nsc_current_feed_code');
		Mage::register('nsc_current_feed_code',$this->getProfile()->getFeed());
		
		$rule = Mage::getModel( 'nscexport/rule');
		$rule->initConditions( $model->getConditions());
		
        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('nscexport')->__('Conditions'),
            'title' => Mage::helper('nscexport')->__('Conditions'),
            'required' => true,
        ))->setRule( $rule)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
				
		$form->addValues($this->getAttributeConfig());
		$form->setFieldNameSuffix('attribute_filter');
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
