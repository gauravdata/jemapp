<?php
class Total_BuckarooCashticket_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckaroocashticket/checkout/form.phtml');
        parent::_construct();
    }
    
    
}