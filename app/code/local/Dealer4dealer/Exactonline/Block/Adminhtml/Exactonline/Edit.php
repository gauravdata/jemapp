<?php
class Dealer4dealer_Exactonline_Block_Adminhtml_Exactonline_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){	
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'exactonline';
		$this->_controller = 'adminhtml_exactonline';	
		$this->_updateButton('save', 'label', Mage::helper('exactonline')->__('Save Setting'));
		$this->_updateButton('delete', 'label', Mage::helper('exactonline')->__('Delete Setting'));

		$fieldId = $this->getRequest()->getParam('id',0);		
		
		if($fieldId>0) {
			$field = Mage::registry('exactonline_data');
			
			if(!(bool)$field->getIsDeletable()) {
				$this->_removeButton('delete');
			}
		}
	}	
	
	public function getHeaderText(){
		if( Mage::registry('exactonline_data') && Mage::registry('exactonline_data')->getId() ) {
			return Mage::helper('exactonline')->__("Edit Setting '%s'", $this->htmlEscape(Mage::registry('exactonline_data')->getName()));
		} else {
			return Mage::helper('exactonline')->__('Add Setting');
		}
	}
}