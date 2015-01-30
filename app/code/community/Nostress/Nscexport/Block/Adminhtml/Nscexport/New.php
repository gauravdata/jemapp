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

class Nostress_Nscexport_Block_Adminhtml_Nscexport_New extends Mage_Adminhtml_Block_Widget_Form_Container
{
	protected $_categoryIds;
	protected $_selectedNodes = null;
	
	public function __construct() {
		parent::__construct();
		$this->_objectId = 'id_new';
		$this->_blockGroup = 'nscexport';
		$this->_controller = 'adminhtml_nscexport';
		$this->_mode = 'new';
		
		$this->_addButton('next_step', array(
                  'label' => Mage::helper('nscexport')->__('Next Step'),
                  'onclick' => 'continueCreate()'
		), -100);
		$this->_removeButton('save');
		
		$this->addLiveChatButton();
		
		$this->_formScripts[] = '
			var productTemplateSyntax = /(^|.|\r|\n)({{(\w+)}})/;
			
			function continueCreate(select) {
				editForm.submit($("edit_form").action);
			}
			
			function setSettings(urlTemplate, setElement) {
				var template = new Template(urlTemplate, productTemplateSyntax);
				setLocation(template.evaluate({attribute_set:$F(setElement)}));
			}';
		$this->_formScripts[] = "
			function showOption(select) {
				select = $(select);
				for (var i = 0, l = select.options.length; i<l; i++) {
					$$('.'+select.id+'_'+select.options[i].value).each(function (el) {
						elsearchengine.style.display = select.selectedIndex==i ? '' : 'none';
					});
				}
			}
			
			function changeType(feedSelect) {
				new Ajax.Updater(
					{success: 'nscexport_type'},
					'".$this->getUrl('adminhtml/nscexport_action/getTypeByFeed')."', {
						asynchronous: true,
						evalScripts: false,
						onComplete: function(request, json) {
							Element.hide('nscexport_type');
							Element.show('nscexport_type');
							changeFile(document.getElementById('nscexport_type'));
						},
						onLoading: function(request, json){},
						parameters: {feed: feedSelect.options[feedSelect.selectedIndex].value}
				});
			}
			
			function changeFile(typeSelect) {
				new Ajax.Updater(
					{success: 'nscexport_file'},
					'".$this->getUrl('adminhtml/nscexport_action/getFileByType')."', {
						asynchronous: true,
						evalScripts: false,
						onComplete: function(request, json) {
							Element.hide('nscexport_file');
							Element.show('nscexport_file');
						},
						onLoading: function(request, json){},
						parameters: {type: typeSelect.options[typeSelect.selectedIndex].value, feed: $('nscexport_feed').options[$('nscexport_feed').selectedIndex].value}
				});
			}";
	}
	
	protected function addLiveChatButton() {
	
	    $this->_addButton('livechat', $this->helper('nscexport')->getLivechatButtonOptions(), -100);
	}
	
	protected function _prepareLayout() {
		if ($this->_blockGroup && $this->_controller && $this->_mode) {
			$this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form'));
		}
		return parent::_prepareLayout();
	}
	
	public function getHeaderText() {
		return Mage::helper('nscexport')->__('Koongo Connector')." - ".Mage::helper('nscexport')->__('New Profile').' ('.Mage::helper('nscexport')->__('Step').' 1)';
	}
}