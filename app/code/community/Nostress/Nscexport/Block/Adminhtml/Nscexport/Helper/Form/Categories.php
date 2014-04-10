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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Categories extends Varien_Data_Form_Element_Abstract
{
	public function getElementHtml() {
		$elementAttributeHtml = '';
		$form = $this->getForm();
		$values = $this->getValues();
		//$categoryRoot = $values["categoryRoot"];
		$categoryFilter = $form->getParent()->getLayout()->createBlock('nscexport/adminhtml_nscexport_edit_product_categoryfilter')->setData(
				array(
					'form' => $form,
					'export_id' => $values["profile_id"],
					'category' => $values["categoryRoot"], //$categoryRoot,
					'store' => $values["store"]
				))->toHtml();

		$html = $categoryFilter;
		
		return $html;
	}
	
	public function getRows() {
		$html = "";
		return $html;
	}
	
	protected function _getRowTemplateHtml($attribute, $key) {
		//print_r($attribute);
		
		$attributesConfig = array(
			"id" => "nscexport_magentoattribute",
			"name" => "attributes[".$attribute["code"]."][magento]",
			"style" => "width: 130px;",
			"values" => Mage::helper('nscexport/data_feed')->getAttributeOptions(),
			"value" => $attribute["magento"]
		);
		
		$attributesSelect = new Varien_Data_Form_Element_Select($attributesConfig);
		$attributesSelect->setForm($this->getForm());
		
		$parentConfig = array(
			"id" => "nscexport_parentconfig",
			"name" => "attributes[".$attribute["code"]."][eppav]",
			"style" => "width: 205px;",
			"values" => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			"value" => (isset($attribute["parent"]) ? $attribute["parent"] : false)
		);
		
		$parentSelect = new Varien_Data_Form_Element_Select($parentConfig);
		$parentSelect->setForm($this->getForm());
		
		$postConfig = array(
			"id" => "nscexport_postconfig",
			"name" => "attributes[".$attribute["code"]."][postproc][]",
			"style" => "width: 205px;",
			"values" => Mage::helper('nscexport/data_feed')->getPostProcessFunctionOptions(),
			"value" => (isset($attribute["post"]) ? $attribute["post"] : false)
		);
		
		$postSelect = new Varien_Data_Form_Element_Multiselect($postConfig);
		$postSelect->setSize(3);
		$postSelect->setForm($this->getForm());
		
		$html = '
		<tr>
			<td style="display: table-cell;">'.$key.'</td>
			<td style="display: table-cell;">'.(isset($attribute["label"]) ? $attribute["label"] : (isset($attribute["code"]) ? $attribute["code"] : "") ).' <abbr title="'.$attribute["description"]["text"].((isset($attribute["description"]["example"]) && !empty($attribute["description"]["example"])) ? "\n\n".Mage::helper('nscexport')->__('Example:')." ".$attribute["description"]["example"] : "").'" style="cursor: help;">(?)</abbr></td>
			<td style="display: table-cell;"><input type="text" value="'.(isset($attribute["prefix"]) ? $attribute["prefix"] : "").'" name="attribute['.$attribute["code"].'][prefix]" style="width: 100px;" /></td>
			<td style="display: table-cell;"><input type="text" value="'.(isset($attribute["constant_value"]) ? $attribute["constant_value"] : "").'" name="attributes['.$attribute["code"].'][constant]" style="width: 150px;" /></td>
			<td style="display: table-cell;">'.$attributesSelect->getElementHtml().'</td>
			<td style="display: table-cell;"><input type="text" value="'.(isset($attribute["suffix"]) ? $attribute["suffix"] : "").'" name="attributes['.$attribute["code"].'][suffix]" style="width: 100px;" /></td>
			<td style="display: table-cell;">'.$parentSelect->getElementHtml().'</td>
			<td style="display: table-cell;"><input type="text" value="'.(isset($attribute["chars_limit"]) ? $attribute["chars_limit"] : "").'" name="attributes['.$attribute["code"].'][limit]" style="width: 100px;" /></td>
			<td style="display: table-cell;">'.$postSelect->getElementHtml().'</td>
		</tr>';
		
		return $html;
	}
	
	/**
	* Dublicate interface of Varien_Data_Form_Element_Abstract::setReadonly
	*
	* @param bool $readonly
	* @param bool $useDisabled
	* @return Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Apply
	*/
	public function setReadonly($readonly, $useDisabled = false) {
		$this->setData('readonly', $readonly);
		$this->setData('disabled', $useDisabled);
		return $this;
	}
}