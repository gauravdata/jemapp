<?php
class Total_BuckarooGiftcard_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckaroogiftcard/checkout/form.phtml');
        parent::_construct();
    }
    
    
}