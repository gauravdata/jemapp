<?php
class Total_BuckarooCollect_Block_Checkout_Form extends Mage_Payment_Block_Form
{

    public function __construct()
    {
		$this->setTemplate('buckaroocollect/checkout/form.phtml');
        parent::_construct();
    }
    
    
}