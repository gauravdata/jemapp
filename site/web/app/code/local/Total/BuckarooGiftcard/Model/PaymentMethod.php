<?php
class Total_BuckarooGiftcard_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'buckaroogiftcard';

    protected $_formBlockType = 'buckaroogiftcard/checkout_form';
    /**
     * Here are examples of flags that will determine functionality availability
     * of this module to be used by frontend and backend.
     * 
     * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
     *
     * It is possible to have a custom dynamic logic by overloading
     * public function can* for each flag respectively
     */
     
    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    
    
    public function getQuote() {
        return $this->getCheckout()->getQuote();
    }

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }
    
    public function getOrderPlaceRedirectUrl()
    {
    	// save quote_id
        Total_Buckaroo_Model_PaymentMethod::saveQuoteId();
        
        $session = Mage::getSingleton('core/session');

    	$session->setData(
    		'additional_fields',
    		array()
    	);
    	
    	return Mage::getUrl('buckaroogiftcard/checkout/redirect', array('_secure' => true));
    }

}
?>