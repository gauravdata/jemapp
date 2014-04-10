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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab_Feed extends Nostress_Nscexport_Block_Adminhtml_Nscexport_Tab
{		
	public function __construct() {
		parent::__construct();
		$this->setShowGlobalIcon(true);
	}
	
	private function capitalize($string) {
		$return = "";
		$tags = explode("_", $string);
		
		foreach ($tags as $tag) {
			$return .= ucfirst($tag);
		}
		
		return $return;
	}
	
	public function _prepareLayout() {
		parent::_prepareLayout();
		
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('_feed');
		$form->setDataObject($this->getProfile());
		$feed = $this->getProfile()->getFeedObject();
		$yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
		$fieldset = $form->addFieldset('common_fieldset', array('legend' => Mage::helper('nscexport')->__('Common')));
		$fieldset->setHeaderBar($this->getHelpButtonHtmlByFieldset("feed_common"));
		
		$config = $this->getFeedConfig();
		$config = $this->arrayField($config,"common",array());
		
		$fieldset->addField('nscexport_feed', 'text', array(
			'label' => Mage::helper('nscexport')->__("Feed:"),
			'name' => "feed",
			'disabled' => true,
			'value' => $feed->getLink()
		));
		
		$fieldset->addField('nscexport_type', 'text', array(
			'label' => Mage::helper('nscexport')->__("Type:"),
			'name' => "type",
			'disabled' => true,
			'value' => $feed->getType()
		));
		
		$fieldset->addField('nscexport_file', 'text', array(
			'label' => Mage::helper('nscexport')->__("File:"),
			'name' => "file",
			'disabled' => true,
			'value' => $feed->getFileType()
		));
		
		$fieldset->addField('encoding', 'select', array(
			'label' => Mage::helper('nscexport')->__("Encoding:"),
			'name' => self::COMMON_PATH."[encoding]",
			'values' => Mage::getSingleton('nscexport/config_source_encoding')->toOptionArray()				
		));
		
		$fieldset->addField('decimal_delimiter', 'select', array(
			'label' => Mage::helper('nscexport')->__("Decimal delimiter:"),
			'name' => self::COMMON_PATH."[decimal_delimiter]",
			'values' => Mage::helper('nscexport/data_source')->getDelimitersOptionArray()			
		));
		
		$fieldset->addField("price_format", 'select', array(
			'label' => Mage::helper('nscexport')->__("Price format:"),
			'name' => self::COMMON_PATH."[price_format]",
			'values' => Mage::getSingleton('nscexport/config_source_priceformat')->toOptionArray()			
		));
		
		$fieldset->addField("datetime_format", 'select', array(
		'label' => Mage::helper('nscexport')->__("Datetime format").":",
		'name' => self::COMMON_PATH."[datetime_format]",
		'values' => Mage::getSingleton('nscexport/config_source_datetimeformat')->toOptionArray()			
		));
		
		$fieldset->addField('category_path_delimiter', 'text', array(
				'label' => Mage::helper('nscexport')->__("Category path delimiter").":",
				'name' => self::COMMON_PATH."[category_path_delimiter]",
		));
		$fieldset->addField('category_lowest_level', 'select', array(
				'label' => Mage::helper('nscexport')->__("Category lowest level").":",
				'name' => self::COMMON_PATH."[category_lowest_level]",
				'values' => Mage::helper('nscexport/data_source')->getCategoryLevelOptionArray(),
				'value' => Nostress_Nscexport_Helper_Data_Source::DEF_CATEGORY_LOWEST_LEVEL
		));
				
		if ($feed->isFileText()) {
			$fieldset->addField('text_enclosure', 'select', array(
				'label' => Mage::helper('nscexport')->__("Text enclosure:"),
				'name' => self::COMMON_PATH."[text_enclosure]",
				'values' => Mage::helper('nscexport/data_source')->getEnclosureOptionArray()				
			));
			
			$fieldset->addField('column_delimiter', 'select', array(
				'label' => Mage::helper('nscexport')->__("Column delimiter:"),
				'name' => self::COMMON_PATH."[column_delimiter]",
				'values' => Mage::helper('nscexport/data_source')->getColumnDelimiterOptionArray()				
			));
			
			$fieldset->addField('new_line', 'select', array(
				'label' => Mage::helper('nscexport')->__("New line character:"),
				'name' => self::COMMON_PATH."[new_line]",
				'values' => Mage::helper('nscexport/data_source')->getNewlineDelimiterOptionArray()				
			));
		}
		
		
		/*$fieldset->addField('nscexport_remove_eol', 'select', array(
			'label' => Mage::helper('nscexport')->__("Remove End of Line Symbols:"),
			'name' => "remove_eol",
			'values' => $yesnoSource
		));*/
		
		/*$fieldset->addField('nscexport_empty', 'select', array(
			'label' => Mage::helper('nscexport')->__("Remove Empty Elements:"),
			'name' => "remove_empty_elements",
			'values' => $yesnoSource
		));*/
		
		$fieldset2 = $form->addFieldset('attributes_fieldset', array('legend' => Mage::helper('nscexport')->__('Feed-specific Attributes')));
		$fieldset2->setHeaderBar($this->getHelpButtonHtmlByFieldset("feed_attributes"));
		
		$fieldset2->addType('attributeselect','Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Attributeselect');
		
		$stock = $this->getAttributeValue("common", "stock");
		
// 		$fieldset2->addField('stock_status_dependence', 'select', array(
// 			'label' => Mage::helper('nscexport')->__("Stock status dependence").":",
// 			'name' => self::COMMON_PATH."[stock][stock_status_dependence]",
// 			'values' => Mage::getSingleton('nscexport/config_source_stockdependence')->toOptionArray(),
// 			'value' => $this->arrayField($stock,"stock_status_dependence",Nostress_Nscexport_Model_Config_Source_Stockdependence::STOCK_AND_QTY) 
// 		));
		
		$fieldset2->addField('nscexport_instock', 'text', array(
			'label' => Mage::helper('nscexport')->__("In stock Value:"),
			'name' => self::COMMON_PATH."[stock][yes]",
			'value' => $this->arrayField($stock,"yes") 
		));
		
		$fieldset2->addField('nscexport_outofstock', 'text', array(
			'label' => Mage::helper('nscexport')->__("Out of stock Value:"),
			'name' => self::COMMON_PATH."[stock][no]",
			'value' => $this->arrayField($stock,"no")
		));
		
		$attributeOptions = Mage::helper('nscexport/data_feed')->getAttributeOptionsAll($this->getProfile()->getStoreId(),$feed->getCode());
		$value = $this->arrayField($stock,"availability");
		$style = $this->getAttributeStyle($attributeOptions, $value); 
		
		$fieldset2->addField('nscexport_exportaout', 'attributeselect', array(
			'label' => Mage::helper('nscexport')->__("Attribute to export if Out of stock").":",
			'name' => self::COMMON_PATH."[stock][availability]",
			'values' => $attributeOptions,
			'value' => $this->arrayField($stock,"availability"),
			'style' => $style,
			'onchange' => 'showWarning(\'TO_BE_REPLACED_WITH_ID\',false);',
		));
		
		$sortAttributes = Mage::helper('nscexport/data_feed')->getAttributeOptions($this->getProfile()->getStoreId(),false,"",true,$feed->getCode());
		array_shift($sortAttributes);
		array_unshift($sortAttributes,array("label" => $this->__("Default (Product id)"),"value" => ""));
		
		$fieldset2->addField('sort_attribute', 'attributeselect', array(
			'label' => Mage::helper('nscexport')->__("Attribute for products sorting").":",
			'name' => self::COMMON_PATH."[sort_attribute]",
			'values' => $sortAttributes,			
			'style' => $style,
			'onchange' => 'showWarning(\'TO_BE_REPLACED_WITH_ID\',false);',
		));
		
		$fieldset2->addField('sort_order', 'attributeselect', array(
			'label' => Mage::helper('nscexport')->__("Products sort order").":",
			'name' => self::COMMON_PATH."[sort_order]",
			'values' => Mage::getSingleton('nscexport/config_source_sortorder')->toOptionArray(),				
			'onchange' => 'showWarning(\'TO_BE_REPLACED_WITH_ID\',false);',
		));
		
		
		$customParams = $this->getAttributeValue("common", "custom_params");
		if(isset($customParams) && is_array($customParams) && array_key_exists("param",$customParams))
		{
			$customParams = $customParams["param"];			
			if (count($customParams) > 0 && is_array($customParams)) {
				$index = 0;
				foreach ($customParams as $parameter) 
				{
					$label = $this->arrayField($parameter,"label");
					if(!isset($label))
						continue;
					$fieldset2->addField('nscexport_'.$this->arrayField($parameter,"code"),$this->arrayField($parameter,"format"), array(
						'label' => Mage::helper('nscexport')->__($label),
						'name' => self::COMMON_PATH."[custom_params][param][".$index."][value]",
						'note' => Mage::helper('nscexport')->__($this->arrayField($parameter,"description")),
						'value' => $this->arrayField($parameter,"value")
					));
					$index++;
				}
			}
		}				
		
		$fieldsetShipping = $form->addFieldset('shipping', array('legend' => Mage::helper('nscexport')->__('Shipping Cost Settings')));
		$fieldsetShipping->setHeaderBar($this->getHelpButtonHtmlByFieldset("feed_shipping"));		
		$fieldsetShipping->addType('attributeselect','Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Attributeselect');
		$fieldsetShipping->addType('shippingcost','Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Shippingcost');
		
		$shipping = $this->getAttributeValue("common","shipping");
		$priceAttributeOptions = Mage::helper('nscexport/data_feed')->getAttributeOptions($this->getProfile()->getStoreId(),false,"price",false,$feed->getCode());
		$dependentAttribute = $this->arrayField($shipping,"dependent_attribute");
		$style = $this->getAttributeStyle($priceAttributeOptions, $dependentAttribute); 
		
		$fieldsetShipping->addField('shipping_attribute', 'attributeselect', array(
			'label' => Mage::helper('nscexport')->__("Shipping costs dependent on").":",
			'name' => self::COMMON_PATH."[shipping][dependent_attribute]",
			'values' => $priceAttributeOptions,
			'value' => $dependentAttribute,
			'style' => $style,
			'onchange' => 'showWarning(\'TO_BE_REPLACED_WITH_ID\',false);',
		));
		
		$fieldsetShipping->addField('shipping_method_name', 'text', array(
			'label' => Mage::helper('nscexport')->__("Shipping method name").":",
			'name' => self::COMMON_PATH."[shipping][method_name]",
			'value' => $this->arrayField($shipping,"method_name")
		));
		
		$fieldsetShipping->addField('shipping_cost_setup', 'shippingcost', array(
			'label' => Mage::helper('nscexport')->__("Shipping costs intervals"),
			'intervals' => $this->arrayField($shipping,"cost_setup"),
			'name' => self::COMMON_PATH."[shipping][cost_setup]",
		), 'frontend_class');
		
					
		$fieldset3 = $form->addFieldset('attributes_map_fieldset', array('legend' => Mage::helper('nscexport')->__('Attributes Mapping Table')));
		$fieldset3->setHeaderBar($this->getHelpButtonHtmlByFieldset("feed_amt"));
		
		$fieldset3->addType('attributes','Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Attributes');
		
		$fieldset3->addField('attributes_map_help', 'label', array(
			'value' => Mage::helper('nscexport')->__("Map product attributes onto feed elements using the attributes mapping table below."),
			'name' => "attributes_map_help",			 
		));

		$attributes = $this->getAttributeValue("attributes", "attribute");
		if(empty($attributes))
		{
			$fieldset3->addField('attributes_map_help_link', 'link', array(
				'value' => Mage::helper('nscexport')->__('Get help for custom feed layout'),
				'target' => "_blank",
				'name' => "attributes_map_help_link",		
				'href' => "https://docs.koongo.com/display/KoongoConnector/Custom+Feed+Layout+Setup" 
			));
		}
		
		$fieldset3->addField('nscexport_attributes_map', 'attributes', array(
			'values' => array("attribute" => $attributes),
			'file' => $feed->getFileType(),
			'store_id' => $this->getProfile()->getStoreId(),
			'feed' => $this->getProfile()->getFeed(),
			'allow_custom_attributes' => $this->arrayField($config,"allow_custom_attributes","0")
		), 'frontend_class');
		
		if(isset($config["encoding"]))
			$config["encoding"] = strtolower($config["encoding"]);
		
		$form->addValues($config);
		
		$form->setFieldNameSuffix('feed');
		$this->setForm($form);
	}
	
	protected function getAttributeStyle($options, $value)
	{
		$style = '';
		if ($options) {
            foreach ($options as $option) {
                if (is_array($option) && !is_array($option['value'])) {
                    if (isset($option['red']) && $option['red'] == 1 && isset($option['value']) && $option['value'] == $value) {
                    	$style = 'color:red';
                    	break;
                    }
                }
            }
        }
        return $style;
	}
}
