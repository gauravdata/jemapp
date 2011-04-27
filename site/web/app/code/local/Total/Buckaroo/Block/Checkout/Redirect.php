<?php
class Total_Buckaroo_Block_Checkout_Redirect extends Mage_Core_Block_Abstract
{
    public $payment_method=NULL;
    
    protected function _toHtml()
    {
        $order_id=Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order=Mage::getModel('sales/order')->loadByIncrementId($order_id);
        
        $customer_id=$order->getCustomerId()?$order->getCustomerId():$order_id;
        
        $TAX=0;
        foreach($order->getFullTaxInfo() as $tax_record)
        {
            $TAX+=$tax_record['amount'];
        }
        
        $TAX=round($TAX*100,0);
       
        $billing_info = $order->getBillingAddress(); 
    
        $firstname=$billing_info->getFirstname();
        $lastname=$billing_info->getLastname();
        $city=$billing_info->getCity(); 
        $state=$billing_info->getState();
        $address=$billing_info->getStreetFull();
        
        $zip=$billing_info->getPostcode();
        
        $billing_country=Mage::getModel('directory/country');
        
        $country_code=$billing_info->getCountry();
        $country=$country_code;
        
        $email=$order->getCustomerEmail();

        $telephone=$billing_info->getTelephone();
        $fax=$billing_info->getFax();
        
        $form = new Varien_Data_Form();
        $form->setAction(Mage::getStoreConfig('payment/'.$this->_code.'/gateway', Mage::app()->getStore()->getStoreId())?Mage::getStoreConfig('payment/'.$this->_code.'/gateway', Mage::app()->getStore()->getStoreId()):'https://payment.buckaroo.nl/gateway/payment.asp')
            ->setId('pay')
            ->setName('pay')
            ->setMethod('POST')
            ->setUseContainer(true);
        
        $form->addField('BPE_Merchant', 'hidden', array('name'=>'BPE_Merchant', 'value'=>Mage::getStoreConfig('payment/buckaroo/key', Mage::app()->getStore()->getStoreId())));
        $form->addField('BPE_Mode', 'hidden', array('name'=>'BPE_Mode', 'value'=>Mage::getStoreConfig('payment/buckaroo/mode', Mage::app()->getStore()->getStoreId())));
        
        $currency=$order->getOrderCurrency();
        
        $form->addField('BPE_Currency', 'hidden', array('name'=>'BPE_Currency', 'value'=>$currency->getCode()));
        
        $form->addField('BPE_Description', 'hidden', array('name'=>'BPE_Description', 'value'=>Mage::getStoreConfig('payment/buckaroo/payment_description', Mage::app()->getStore()->getStoreId())));
        
        $locale=Mage::app()->getLocale()->getLocaleCode();
       
        list($language)=explode("_",$locale);
        $language=strtoupper($language);

            switch($language) {
                case "DE": $language = "DE"; break;
                case "DK": $language = "DK"; break;
                case "EN":
                case "GB": $language = "EN"; break;
                case "ES": $language = "ES"; break;
                case "FR": $language = "FR"; break;
                case "IT": $language = "IT"; break;
                case "NL": $language = "NL"; break;
                case "PT": $language = "PT"; break;
                case "RU": $language = "RU"; break;
                case "SE": $language = "SE"; break;
                
                default: $language = "NL"; break;
        }
        
        $form->addField('BPE_Language', 'hidden', array('name'=>'BPE_Language', 'value'=>$language));
        
        $form->addField('BPE_Invoice', 'hidden', array('name'=>'BPE_Invoice', 'value'=>$order_id));
        
        $amount=round($order->getGrandTotal()*100,0);
        
        $form->addField('BPE_Invoicevat', 'hidden', array('name'=>'BPE_Invoicevat', 'value'=>$TAX)); 
        $form->addField('BPE_Amount', 'hidden', array('name'=>'BPE_Amount', 'value'=>$amount));
        
        $BPE_paymentmethodsallowed='';
        if(isset($this->payment_method))
        {
            $BPE_paymentmethodsallowed=Mage::getStoreConfig('payment/buckaroo'.$this->payment_method.'/allowed_methods', Mage::app()->getStore()->getStoreId())?Mage::getStoreConfig('payment/buckaroo'.$this->payment_method.'/allowed_methods', Mage::app()->getStore()->getStoreId()):$this->payment_method;
        }
        else
        {
            $BPE_paymentmethodsallowed=Mage::getStoreConfig('payment/buckaroo/allowed_methods', Mage::app()->getStore()->getStoreId());
        }
        
        $form->addField('BPE_paymentmethodsallowed', 'hidden', array('name'=>'BPE_paymentmethodsallowed', 'value'=>$BPE_paymentmethodsallowed));
        $form->addField('BPE_paymentmethodsallowed_cm', 'hidden', array('name'=>'BPE_paymentmethodsallowed_cm', 'value'=>Mage::getStoreConfig('payment/buckaroo/allowed_methods_cm', Mage::app()->getStore()->getStoreId())));
        
        $form->addField('BPE_paymentdatedue', 'hidden', array('name'=>'BPE_paymentdatedue', 'value'=>date("Y-m-d H:i:s", strtotime("now + " . Mage::getStoreConfig('payment/buckaroo/cm_due_minutes', Mage::app()->getStore()->getStoreId()) . " minutes"))));
        $form->addField('BPE_invoicedate', 'hidden', array('name'=>'BPE_invoicedate', 'value'=>date("Y-m-d H:i:s", strtotime("now + " . Mage::getStoreConfig('payment/buckaroo/cm_invoice_minutes', Mage::app()->getStore()->getStoreId()) . " minutes"))));

//      We will ignore customer_id, this gives error in calculating the HASH for buckaroo               
//      $form->addField('BPE_customerid', 'hidden', array('name'=>'BPE_customerid', 'value'=>$customer_id));
        
        $form->addField('BPE_customerfirstname', 'hidden', array('name'=>'BPE_customerfirstname', 'value'=>$firstname));
        $form->addField('BPE_customerlastname', 'hidden', array('name'=>'BPE_customerlastname', 'value'=>$lastname));
        
        
        $form->addField('BPE_customergender', 'hidden', array('name'=>'BPE_customergender', 'value'=>null));
                
        $form->addField('BPE_customeraddress1', 'hidden', array('name'=>'BPE_customeraddress1', 'value'=>$address));
        
        $form->addField('BPE_customerzipcode', 'hidden', array('name'=>'BPE_customerzipcode', 'value'=>$zip));
        $form->addField('BPE_customercity', 'hidden', array('name'=>'BPE_customercity', 'value'=>$city));
        $form->addField('BPE_customercountry', 'hidden', array('name'=>'BPE_customercountry', 'value'=>$country));
        $form->addField('BPE_customermail', 'hidden', array('name'=>'BPE_customermail', 'value'=>$email));
        $form->addField('BPE_customerphone', 'hidden', array('name'=>'BPE_customerphone', 'value'=>$telephone));
        $form->addField('BPE_customerstate', 'hidden', array('name'=>'BPE_customerstate', 'value'=>$state));
        $form->addField('BPE_customerfax', 'hidden', array('name'=>'BPE_customerfax', 'value'=>$fax));
        
        
        // $form->addField('BPE_Reference', 'hidden', array('name'=>'BPE_Reference', 'value'=>$language));
        
        
        // addition fields
        $customerId = $order->getCustomerId() ? $order->getCustomerId() : $order_id;
        
        $form->addField('BPE_customerid', 'hidden', array('name'=>'BPE_customerid', 'value'=>$customerId));
        $form->addField('BPE_customername', 'hidden', array('name'=>'BPE_customername', 'value'=>$firstname.' '.$lastname));
        $form->addField('BPE_reference', 'hidden', array('name'=>'BPE_reference', 'value'=>$order_id));
        $form->addField('BPE_showform', 'hidden', array('name'=>'BPE_showform', 'value'=>Mage::getStoreConfig('payment/buckaroo/show_form', Mage::app()->getStore()->getStoreId())));
        
        $shippingInfo = $order->getShippingAddress();
        $shippingCountry = $shippingInfo->getCountry();
        
        $form->addField('bpe_locale', 'hidden', array('name' => 'bpe_locale', 'value' => self::getLocaleByCountry($shippingCountry)));
        
        // Additional fields
        $session = Mage::getSingleton('core/session');
        $additional_fields=$session->getData('additional_fields');
        if(is_array($additional_fields) && !empty($additional_fields))
        {
            foreach($additional_fields as $key=>$field)
            {
                if(!is_array($field))
                {
                    // BPE fields must look alike
                    if(strpos($key,'BPE_')==0)
                    {
                        $key=strtolower($key);
                        $key=str_replace('bpe_', 'BPE_', $key);
                    }
                    
                    // if such key already exists in the form - rewrite it with the new field value
                    if($form->getElements()->searchById($key))
                    {
                        if(strlen(trim($field)))
                        $form->getElements()->searchById($key)->setValue($field);
                    }
                    else
                    {
                        $form->addField($key,'hidden',array('name'=>$key, 'value'=>$field));    
                    }
                }
            }
        }
        $session->unsetData('additional_fields');

        $signature2=md5(
            Mage::getStoreConfig('payment/buckaroo/key') . 
            $order_id .
            $amount .
            $currency->getCode().
            Mage::getStoreConfig('payment/buckaroo/mode') .
            $customerId .
            Mage::getStoreConfig('payment/buckaroo/digital_signature')
        );
        
        $url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . (Mage::getStoreConfig('web/seo/use_rewrites', Mage::app()->getStore()->getStoreId())!=1?'index.php/':'') . (Mage::getStoreConfig('web/url/use_store', Mage::app()->getStore()->getStoreId()) != 1 ? '' : Mage::app()->getStore()->getCode() . '/');
        
        $form->addField('BPE_Signature2', 'hidden', array('name'=>'BPE_Signature2', 'value'=>$signature2));
        $form->addField('bpe_return_type', 'hidden', array('name'=>'bpe_return_type', 'value'=>'PAGE'));
        $form->addField('bpe_return_success', 'hidden', array('name'=>'bpe_return_success', 'value'=>$url.'buckaroo/checkout/success')); 
        $form->addField('bpe_return_reject', 'hidden', array('name'=>'bpe_return_reject', 'value'=>$url.'buckaroo/notify/error'));                  
        $form->addField('bpe_return_error', 'hidden', array('name'=>'bpe_return_error', 'value'=>$url.'buckaroo/notify/error'));                    
        $form->addField('bpe_return_method', 'hidden', array('name'=>'bpe_return_method', 'value'=>'POST'));                            
        $form->addField('bpe_autoclose_popup', 'hidden', array('name'=>'bpe_autoclose_popup', 'value'=>'FALSE'));

        /*if ($this->payment_method == 'giftcard') {
            $form->addField('bpe_locale', 'hidden', array('name' => 'bpe_locale', 'value' => substr($locale, 0, 2) . '-NL'));
            // $form->addField('bpe_signature3', 'hidden', array('name'=>'bpe_signature3', 'value' => $this->_getSignature3($form)));
        }*/
        
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to the Buckaroo in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("pay").submit();</script>';    
        $html.= '</body></html>';
        
        return $html;
    }

    /**
     * Create signature type 3
     * 
     * @param object $form
     * @return string
     */
    protected function _getSignature3($form)
    {
        $keyToSort = array();
        $value = array();

        foreach ($form->getElements() as $element) {
            $name = strtolower($element->getName());
            if (substr($name, 0, 4) == 'bpe_') {
                $keyToSort[substr($name, 4)] = $element->getName();
                $value[substr($name, 4)] = trim($element->getValue());
            }
        }

        ksort($keyToSort);

        foreach ($keyToSort as $key => $val) {
            $string .= "{$val}={$value[$key]};";
        }

        return sha1($string . Mage::getStoreConfig('payment/buckaroo/key', Mage::app()->getStore()->getStoreId()));
    }
    
    /**
     * Get locale by Shipping country
     * 
     * @param string $countryCode
     * @return string
     */
    private function getLocaleByCountry($countryCode = 'NL')
    {
    	$lang = '';
    	switch($countryCode) {
    		case 'US': 
    		case 'GB': 
    		case 'AU': 
    		case 'NZ': $lang = 'en'; break;
    		case 'AT': 
    		case 'DE': 
    		case 'CH': $lang = 'de'; break;
    		case 'CA': 
    		case 'BE':
    		case 'FR': $lang = 'fr'; break;
    		case 'AR': 
    		case 'CL': 
    		case 'CO': 
    		case 'CR': 
    		case 'MX': 
    		case 'PA':
    		case 'PE': 
    		case 'VE':
    		case 'ES': $lang = 'es'; break;
    		case 'NL': $lang = 'nl'; break;
    		default: return 'en-US';
    	}
    	return $lang . '-' . $countryCode;
    }
}