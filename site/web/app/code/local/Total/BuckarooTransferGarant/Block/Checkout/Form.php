<?php
class Total_BuckarooTransferGarant_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckarootransfergarant/checkout/form.phtml');
        parent::_construct();
    }
    
    
}