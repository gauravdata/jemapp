<?php
class Total_BuckarooGiropay_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckaroogiropay/checkout/form.phtml');
        parent::_construct();
    }
    
    
}