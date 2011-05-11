<?php
class Total_ActiveQuoteAfterCheckout_Model_Observer extends Mage_Core_Model_Abstract {

    protected $_allowed_methods=array('buckaroo',
                                      'buckaroocashticket',
                                      'buckaroocc',
                                      'buckaroocollect',
                                      'buckaroogiftcard',
                                      'buckaroogiropay',
                                      'buckarooideal',
                                      'buckaroopaypal',
                                      'buckaroopayperemail',
                                      'buckaroopaysafecard',
                                      'buckarootransfer',
                                      'buckarootransfergarant');
    
    public function sales_model_service_quote_submit_after(Varien_Event_Observer $observer) {
       
       $method=$observer->getQuote()->getPayment()->getMethod();
       
       if(in_array($method, $this->_allowed_methods))
       {
           // Activate the quote 
           $observer->getQuote()->setIsActive(true);
       }
            
    }
}
