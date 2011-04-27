<?php
class Total_BuckarooPayperemail_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckaroopayperemail/checkout/form.phtml');
        parent::_construct();
    }
    
    
}