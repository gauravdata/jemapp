<?php
class Total_BuckarooPaypal_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckaroopaypal/checkout/form.phtml');
        parent::_construct();
    }
    
    
}