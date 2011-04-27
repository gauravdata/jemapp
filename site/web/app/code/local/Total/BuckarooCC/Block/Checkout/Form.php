<?php
class Total_BuckarooCC_Block_Checkout_Form extends Mage_Payment_Block_Form_Cc
{

    public function __construct()
    {
		parent::_construct();
		
    	$this->setTemplate('buckaroocc/checkout/form.phtml');
        
    }
    
    public function getCcAvailableTypes()
    {
    	return array (
						  'AE' => 'American Express',
						  'VI' => 'Visa',
						  'MC' => 'Master Card',
					  );
    }
    
} 