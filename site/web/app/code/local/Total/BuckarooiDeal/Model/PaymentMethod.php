<?php
class Total_BuckarooiDeal_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'buckarooideal';

    protected $_formBlockType = 'buckarooideal/checkout_form';
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
    
    public function getOrderPlaceRedirectUrl()
    {
    	// save quote_id
        Total_Buckaroo_Model_PaymentMethod::saveQuoteId();
        
    	$session = Mage::getSingleton('core/session');
    	
    	if(isset($_POST[$this->_code.'_BPE_Issuer']))
    	{
    		$session->setData('additional_fields',array('BPE_Issuer'=>$_POST[$this->_code.'_BPE_Issuer']));
    	}
    	
    	return Mage::getUrl('buckarooideal/checkout/redirect', array('_secure' => true));
    }
    
    public function isAvailable($quote = null)
    {
    	// availability currency codes for this Payment Module
    	$allowCurrency = array('EUR');
    	
        // get current currency code
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();

        // currency is not available for this module
        if (!in_array($currency, $allowCurrency))
        {
            return false;
        }
                  
        return parent::isAvailable($quote);
    }

}