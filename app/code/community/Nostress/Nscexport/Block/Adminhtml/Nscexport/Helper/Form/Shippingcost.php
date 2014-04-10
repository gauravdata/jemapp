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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_Helper_Form_Shippingcost extends Varien_Data_Form_Element_Abstract
{
	const SHIPPING_SETUP_PATH_FULL = "feed[common][shipping][cost_setup]";
    const INFO_IMAGE_PATH = "adminhtml/default/default/images/note_msg_icon.gif";
    
    protected $_rowIndex = 0;
    protected $_attributeOptions;
    
	public function getElementHtml() {
		$elementAttributeHtml = '';
		$form = $this->getForm();
		
		$addButton = $form->getParent()->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label' => Mage::helper('nscexport')->__('Add new interval'),
				'onclick' => "addInterval()",
				'class' => 'add addIntervalButton'
			));
		$removeButton = $form->getParent()->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label' => Mage::helper('nscexport')->__('Remove interval'),
				'onclick' => "removeInterval()",
				'class' => 'delete',
				'style' => 'margin-right: 10px;'
			));
		
		$addButtonHtml =  $addButton->toHtml();
		$actionColumnHtml = '<th>'.Mage::helper('nscexport')->__('Action').'</th>';
		
		$html = '
		<div class="grid" ><div class="hor-scroll">
			<script type="text/javascript">
				function showWarning(selectId,resize) {
					var selectedIndex = document.getElementById(selectId).selectedIndex;
					if (document.getElementById(selectId).options[selectedIndex].className == "warning") {
						document.getElementById(selectId+"_warning").style.display="block";
						document.getElementById(selectId).style.color="red";
						if (resize) {
							document.getElementById(selectId).style.width="90%";
						}
					} else {
						document.getElementById(selectId+"_warning").style.display="none";
						document.getElementById(selectId).style.color="black";
						if (resize) {
							document.getElementById(selectId).style.width="100%";
						}
					}
				}
			</script>
			<table class="data" cellspacing="0" id="shipping_container">
					<thead>
					<tr class="headings">
						<th>'.Mage::helper('nscexport')->__('#').'</th>
						<th>'.Mage::helper('nscexport')->__('Product price from').'</th>						
						<th>'.Mage::helper('nscexport')->__('Product price to').'</th>
						<th>'.Mage::helper('nscexport')->__('Shipping cost').'</th>						
						'.$actionColumnHtml.'
					</tr>
					</thead>
					<tbody>
					'.$this->getRows().'
					</tbody>
			</table>
		</div></div>
		<div class="addAttributeButton">			
			'.$addButtonHtml.'
		</div>';
		
		return $html;
	}
	
	public function getRows() {
		$html = "";
		$intervals = $this->getIntervals();		
		$custom = 0;				
		
		$index = 1;
		if (!empty($intervals) && is_array($intervals)) {
			foreach ($intervals as $key => $interval) {
				
				$html .= $this->_getRowTemplateHtml($interval, $index);
				$index++;
			}
		}
		return $html;
	}
	
	protected function _getRowTemplateHtml($interval, $key) {
		$index = $this->_rowIndex;
		$this->_rowIndex++;
		$disabled = "";
		$disabledBool = 0;
		$disabledHtml = "";
		
		$removeElementButton = $this->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label' => Mage::helper('nscexport')->__('Delete'),
				'onclick' => "removeInterval(".$key.")",
				'class' => 'delete'
			));
		
			
		$priceFromId = self::SHIPPING_SETUP_PATH_FULL.'['.$index.'][price_from]';
		$priceToId = self::SHIPPING_SETUP_PATH_FULL.'['.$index.'][price_to]';
		$costId = self::SHIPPING_SETUP_PATH_FULL.'['.$index.'][cost]';
		
		$html = '
		<tr  id="interval'.$key.'"'.'>
			<td style="display: table-cell;">'.$key.'</td>
			<td style="display: table-cell;"><input type="text" id="input_price_from_'.$index.'" value="'.$this->attribute($interval, "price_from").'" name="'.$priceFromId.'" style="width: 95%;" /></td>
			<td style="display: table-cell;"><input type="text" id="input_price_to_'.$index.'" value="'.$this->attribute($interval, "price_to").'" name="'.$priceToId.'" style="width: 95%;" /></td>
			<td style="display: table-cell;"><input type="text" id="input_cost_'.$index.'" value="'.$this->attribute($interval, "cost").'" name="'.$costId.'" style="width: 95%;" /></td>			
			<td>'.$removeElementButton->toHtml().'</td> 
		</tr>';
		
		return $html;
	}
	
	protected function attribute($attributeArray, $index, $default = "") {
		if (isset($attributeArray[$index])) {
			return $attributeArray[$index];
		}
		else {
			return $default;
		}
	}
}