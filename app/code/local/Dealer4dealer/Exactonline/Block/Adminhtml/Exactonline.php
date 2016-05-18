<?php
class Dealer4dealer_Exactonline_Block_Adminhtml_Exactonline extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
    {
		$this->_controller = 'adminhtml_exactonline';
		$this->_blockGroup = 'exactonline';
		$this->_headerText = Mage::helper('exactonline')->__('Exact Online settings');
		$this->_addButtonLabel = Mage::helper('exactonline')->__('Add setting');

        $this->_addButton('import_settings', array(
            'label'     =>  Mage::helper('exactonline')->__('Grand Access'),
            'onclick'   =>  "setLocation('".$this->getUrl('*/adminhtml_auth/grand')."')"
        ));

		parent::__construct();
	}
}