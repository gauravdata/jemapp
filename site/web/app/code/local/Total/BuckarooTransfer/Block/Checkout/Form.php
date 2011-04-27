<?php
class Total_BuckarooTransfer_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckarootransfer/checkout/form.phtml');
        parent::_construct();
    }
    
    
}