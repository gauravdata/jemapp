<?php
class Total_BuckarooPaysafecard_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckaroopaysafecard/checkout/form.phtml');
        parent::_construct();
    }
    
    
}