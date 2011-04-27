<?php
class Total_BuckarooiDeal_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckarooideal/checkout/form.phtml');
        parent::_construct();
    }
    
    
}