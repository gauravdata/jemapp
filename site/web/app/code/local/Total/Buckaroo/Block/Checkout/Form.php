<?php
class Total_Buckaroo_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckaroo/checkout/form.phtml');
        parent::_construct();
    }
    
    
}