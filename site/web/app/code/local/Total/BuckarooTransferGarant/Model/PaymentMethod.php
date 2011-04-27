<?php
class Total_BuckarooTransferGarant_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'buckarootransfergarant';

    protected $_formBlockType = 'buckarootransfergarant/checkout_form';
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
        $session = Mage::getSingleton('core/session');
        
        if(isset($_POST[$this->_code.'_BPE_Customergender']) &&
           isset($_POST[$this->_code.'_BPE_Customername']) &&
           isset($_POST[$this->_code.'_BPE_Customermail']) &&
           isset($_POST[$this->_code.'_bpe_customer_account_number']) &&
           isset($_POST[$this->_code.'_customerbirthdate']['day']) &&
           isset($_POST[$this->_code.'_customerbirthdate']['month']) &&
           isset($_POST[$this->_code.'_customerbirthdate']['year']))
        {         
            $session->setData('additional_fields',array('BPE_Customergender'=>$_POST[$this->_code.'_BPE_Customergender'],
                                                        'BPE_Customername'=>$_POST[$this->_code.'_BPE_Customername'],
                                                        'BPE_Customermail'=>$_POST[$this->_code.'_BPE_Customermail'],
                                                        'bpe_customer_account_number'=>$_POST[$this->_code.'_bpe_customer_account_number'],
                                                        'bpe_customerbirthdate'=>date('Y-m-d', strtotime($_POST[$this->_code.'_customerbirthdate']['year'].'-'.$_POST[$this->_code.'_customerbirthdate']['month'].'-'.$_POST[$this->_code.'_customerbirthdate']['day']))));
        }
        
        return Mage::getUrl('buckarootransfergarant/checkout/redirect', array('_secure' => true));
        
    }
    
    public function isAvailable($quote = null)
    {
        $session=Mage::getSingleton('core/session');
        
        // module is not available if SOAP key is not filled in
        if(!Mage::getStoreConfig('payment/buckaroo/soap_signature', Mage::app()->getStore()->getStoreId()))
        {
            return false;
        }
        
        // module is not available if grand total is greater then max amount
        if(Mage::getStoreConfig('payment/buckarootransfergarant/max_amount', Mage::app()->getStore()->getStoreId()) && $quote->getGrandTotal()>Mage::getStoreConfig('payment/buckarootransfergarant/max_amount', Mage::app()->getStore()->getStoreId()))
        {
            return false;
        }
        
        // module could be not available after unsuccessful attempt
        if(Mage::getStoreConfig('payment/buckarootransfergarant/hide_after_failed', Mage::app()->getStore()->getStoreId()) && $session->getData('hide_BuckarooTransferGarant'))
        {
            return false;
        }
        else
        {
            $session->setData('hide_BuckarooTransferGarant', false);
        }
                   
        return parent::isAvailable($quote);
    }
    
    public function filterDutchPhoneNumber($number) {
        //the final output must like this: 0031123456789 for mobile: 0031612345678
        //so 13 characters max else number is not valid
        //but for some error correction we try to find if there is some faulty notation
        
        $return = array("orginal" => $number, "clean" => false, "mobile" => false, "valid" => false);
        //first strip out the non-numeric characters:
        if($match = preg_replace('/[^0-9]/Uis', '', $number)){
            $number = $match;
        }
        
        if(strlen((string)$number) == 13) {
            //if the length equal to 13 is, then we can check if its a mobile number or normal number
            $return['mobile'] = $this->isMobileNumber($number);
            //now we can almost say that the number is valid
            $return['valid'] = true;
            $return['clean'] = $number;
        }else if(strlen((string)$number) > 13) {
            //if the number is bigger then 13, it means that there are probably a zero to much
            $return['mobile'] = $this->isMobileNumber($number);
            $return['clean'] = $this->isValidNotation($number);
            if(strlen((string)$return['clean']) == 13) {
                $return['valid'] = true;
            }
            
        }else if(strlen((string)$number) == 12 or strlen((string)$number) == 11) {
            //if the number is equal to 11 or 12, it means that they used a + in their number instead of 00 
            $return['mobile'] = $this->isMobileNumber($number);
            $return['clean'] = $this->isValidNotation($number);
            if(strlen((string)$return['clean']) == 13) {
                $return['valid'] = true;
            }
            
        }else if(strlen((string)$number) == 10) {
            //this means that the user has no trailing "0031" and therfore only
            $return['mobile'] = $this->isMobileNumber($number);
            $return['clean'] = '0031'.substr($number,1);
            if(strlen((string)$return['clean']) == 13) {
                $return['valid'] = true;
            }
        }
        
        return $return;
    }
    
    public function isMobileNumber($number) {
        //this function only checks if it is a mobile number, not checking valid notation
        $checkMobileArray = array("3106","316","06","00316","003106");
        foreach($checkMobileArray as $key => $value) {
            
            if( strpos( substr( $number, 0, 6 ), $value ) !== false) {
                
                return true;
            }
        }
        return false;
    }
    
    public function isValidNotation($number) {
        //checks if the number is valid, if not: try to fix it
        $unvalidNotations = array("00310","0310","310","31");
        foreach($unvalidNotations as $invalid) {
            if( strpos( substr( $number,0,6 ),$invalid ) !== false ) {
                $valid = substr($invalid,0,-1);
                if(substr($valid,0,2) == '31'){ $valid = "00".$valid;}
                if(substr($valid,0,2) == '03'){ $valid = "0".$valid;}
                if($valid == '3'){ $valid = "0".$valid."1";}
                $number = str_replace($invalid,$valid,$number);
            }
        }
        return $number;
    }
    
    private function _getHouseNumber($address)
    {
        preg_match('/((?!\s+)|(?!,)|^)((\d\s{1}[a-zA-Z]{1})|(\d[a-zA-Z]{1})|(\d-[a-zA-Z]{1})|(\d))+(([a-zA-Z](?=\s|$))|(?=\s+)|$)/s',$address, $match);
        
        return $match[0]?$match[0]:null;
    }
    
    private function _getAddition($housenumber)
    {
        preg_match('/([^\d]+)/s', $housenumber, $match);
        
        $addition=false;
        
        if($match[0])
        {
            $addition= preg_replace('/([^a-zA-Z])/s','', $match[0]);
        }
        
        return $addition?$addition:'';
    }    
        
    
    
    public function request()
    {
        // voor betaalgarant altijd 4
        $MaxReminderLevel = 4;
        // defaulten naar ideal en overboeking
        $Paymentmethodsallowed = "ideal,transfer";
        // lastname prefix
        $LastnamePrefix = "";
        
        $order_id=Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order=Mage::getModel('sales/order')->loadByIncrementId($order_id);
        
        $VAT=0;
        foreach($order->getFullTaxInfo() as $tax_record)
        {
            $VAT+=$tax_record['amount'];
        }
        
        $VAT=round($AmountTax*100,0);
       
        $billing_info = $order->getBillingAddress();
        
        // Save information about guest
        if(!Mage::helper('customer')->isLoggedIn())
        {
            $session = Mage::getSingleton('core/session');
            
            $session->setData('guest_information',array('firstname'=>$billing_info->getFirstname(),
                                                        'lastname'=>$billing_info->getLastname(),
                                                        'company'=>$billing_info->getCompany(),
                                                        'email'=>$order->getCustomerEmail(),
                                                        'address'=>$billing_info->getStreetFull(),
                                                        'city'=>$billing_info->getCity(),
                                                        'postcode'=>$billing_info->getPostcode(),
                                                        'region'=>$billing_info->getRegion(),
                                                        'region_id'=>$billing_info->getRegionId(),
                                                        'country'=>$billing_info->getCountry(),
                                                        'telephone'=>$billing_info->getTelephone(),
                                                        'fax'=>$billing_info->getFax()));
        }
 
        // Make a customer number unique
        $Id=$order->getCustomerId()?$order->getCustomerId():$order_id;
    
        $Firstname=$billing_info->getFirstname();
        $Lastname=$billing_info->getLastname();
        
        //
        $Firstname_parts=explode(" ", $Firstname);
        foreach($Firstname_parts as $part)
        {
            $Initials.=$part{0};    
        }
        
        $City=$billing_info->getCity(); 
        $State=$billing_info->getRegion();
        $Address1=$billing_info->getStreetFull();
        
        $Housenumber=$this->_getHouseNumber($Address1);
        
        $Street=trim(str_replace($Housenumber,'', $Address1));
        
        $HousenumberSuffix=$this->_getAddition($Housenumber);
        
        // Clear housenumber from additional part
        if($HousenumberSuffix)
        {
            $Housenumber=preg_replace('/([^\d])/s','', $Housenumber);
        }
        
        $Address2=null;
        
        $Zipcode=$billing_info->getPostcode();
        
        $billing_country=Mage::getModel('directory/country');
        
        $country_code=$billing_info->getCountry();
        $Country=$country_code;
        
        $Mail=$order->getCustomerEmail();

        $telArr = $this->filterDutchPhoneNumber($billing_info->getTelephone());
        if($telArr['valid'] == true){
            if($telArr['mobile'] == true) {
                $Gsm = $telArr['clean'];
                $Phone = '';
            }else {
                $Phone = $telArr['clean'];
                $Gsm = '';
            }
            
        }else {
            $Phone = $telArr['original'];
            $Gsm = '';
        }
        
        $Fax=null;
        $Icq=null;        
        $Fax=$billing_info->getFax();
        
        $session = Mage::getSingleton('core/session');
        $additional_fields=$session->getData('additional_fields');
        
        $Birthdate=$additional_fields['bpe_customerbirthdate'];
        $Bankaccountnumber=$additional_fields['bpe_customer_account_number'];
        $Bankaccountnumber=preg_replace("/[^0-9]/s","", $Bankaccountnumber);
        
        $Gender=$additional_fields['BPE_Customergender'];
        $session->unsetData('additional_fields');
         
        $locale=Mage::app()->getLocale()->getLocaleCode();
       
        list($Language,)=explode("_",$locale);
        $Language=strtoupper($Language);

        switch($Language) {
                case "DE": $Language = "DE"; break;
                case "DK": $Language = "DK"; break;
                case "EN":
                case "GB": $Language = "EN"; break;
                case "ES": $Language = "ES"; break;
                case "FR": $Language = "FR"; break;
                case "IT": $Language = "IT"; break;
                case "NL": $Language = "NL"; break;
                case "PT": $Language = "PT"; break;
                case "RU": $Language = "RU"; break;
                case "SE": $Language = "SE"; break;
                
                default: $Language = "NL"; break;
        }
        
        $Locale=str_replace("_","-", $locale);
        
        $Test='FALSE';
        if(Mage::getStoreConfig('payment/buckaroo/mode', Mage::app()->getStore()->getStoreId()))
        {
            $Test='TRUE';
        }
    
        $Timestamp=Mage::getModel('core/date')->date('Y-m-d H:i:s');
        
        $Date=date('Y-m-d', time());
        $Time=date('H:i:s', time());
        
        $MerchantID=Mage::getStoreConfig('payment/buckaroo/key', Mage::app()->getStore()->getStoreId());

        $EmployeeID=null;
        
        if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ClientIp=$_SERVER['HTTP_CLIENT_IP'];
        }
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ClientIp=$_SERVER['REMOTE_ADDR'];
        }
        else 
        {
            $ClientIp=$_SERVER['REMOTE_ADDR'];
        }
        
        $Currency=$currency=$order->getOrderCurrency()->getCode();

        $Amount=round($order->getGrandTotal()*100,0);
        
        $Description=Mage::getStoreConfig('payment/buckaroo/payment_description', Mage::app()->getStore()->getStoreId());
        
        $Invoice=$order_id;
        $SenderSessionID=$Invoice;

        $PaymentDateDue=Mage::getModel('core/date')->date('Y-m-d', 'now + '.(int)Mage::getStoreConfig('payment/buckarootransfergarant/datedue', Mage::app()->getStore()->getStoreId()).' day');
        $InvoiceDate=Mage::getModel('core/date')->date('Y-m-d');

        $Reference=$Invoice;
        
        $SendMail=Mage::getStoreConfig('payment/buckarootransfergarant/sendmail', Mage::app()->getStore()->getStoreId())?'TRUE':'FALSE';

// Clean xml variables
$variablesToClean=array('Gender', 'Firstname', 'Lastname',
                        'Initials', 'Mail', 'Gsm', 'Phone',
                        'Address1', 'Zipcode', 'City',
                        'Country', 'Birthdate', 'AccountNumber',
                        'ClientIp', 'Currency', 'Amount',
                        'AmountTax', 'Datedue', 'SendMail', 'Invoice',
                        'Reference', 'Description');

foreach($variablesToClean as $variable)
{
    $$variable=str_replace(array("\r","\n"), " ", $$variable);
}
// end of cleaning xml variables

$Payload=<<<XML_PART
<Payload VersionID="1.0" xmlns="https://payment.buckaroo.nl/PaymentGuarantee">
         <Control Language="{$Language}" Test="{$Test}">
            <Timestamp Zone="+01:00">{$Timestamp}</Timestamp>
            <MerchantID>{$MerchantID}</MerchantID>
            <SenderSessionID>{$SenderSessionID}</SenderSessionID>
         </Control>
         <Content>
            <Transaction>
                <ClientIp>{$ClientIp}</ClientIp>
                <Amount Currency="{$Currency}">{$Amount}</Amount>
                <Invoice>{$Invoice}</Invoice>
                <Description>{$Description}</Description>
                <Reference>{$Reference}</Reference>
                <SendMail>{$SendMail}</SendMail>
                <CreditManagement>
                    <Invoice>
                        <VAT>{$VAT}</VAT>
                        <PaymentDateDue>{$PaymentDateDue}</PaymentDateDue>
                        <InvoiceDate>{$InvoiceDate}</InvoiceDate>
                        <MaxReminderLevel>{$MaxReminderLevel}</MaxReminderLevel>
                        <Paymentmethodsallowed>{$Paymentmethodsallowed}</Paymentmethodsallowed>
                    </Invoice>
                    <Customer>
                        <ID>{$Id}</ID>
                        <Initials>{$Initials}</Initials>
                        <Firstname>{$Firstname}</Firstname>
                        <LastnamePrefix>{$LastnamePrefix}</LastnamePrefix>
                        <Lastname>{$Lastname}</Lastname>
                        <Gender>{$Gender}</Gender>
                        <Mail>{$Mail}</Mail>
                        <Addresses>
                            <Address Type="INVOICE">
                                <Street>{$Street}</Street>
                                <Housenumber>{$Housenumber}</Housenumber>
                                <HousenumberSuffix>{$HousenumberSuffix}</HousenumberSuffix>
                                <Zipcode>{$Zipcode}</Zipcode>
                                <City>{$City}</City>
                                <State>{$State}</State>
                                <Country>{$Country}</Country>
                            </Address>
                            <Address Type="SHIPPING">
                                <Street>{$Street}</Street>
                                <Housenumber>{$Housenumber}</Housenumber>
                                <HousenumberSuffix>{$HousenumberSuffix}</HousenumberSuffix>
                                <Zipcode>{$Zipcode}</Zipcode>
                                <City>{$City}</City>
                                <State>{$State}</State>
                                <Country>{$Country}</Country>
                            </Address>
                        </Addresses>
                        <Bankaccountnumber>{$Bankaccountnumber}</Bankaccountnumber>
                        <Birthdate>{$Birthdate}</Birthdate>
                        <Fax>{$Fax}</Fax>
                        <Gsm>{$Gsm}</Gsm>
                        <Icq>{$Icq}</Icq>
                        <Msn>{$Msn}</Msn>
                        <Phone>{$Phone}</Phone>
                        <Skype>{$Skype}</Skype>
                        <Title>{$Title}</Title>
                    </Customer>
                </CreditManagement>
            </Transaction>
        </Content>
</Payload>
XML_PART;

        $Fingerprint=md5(Mage::getStoreConfig('payment/buckaroo/soap_signature', Mage::app()->getStore()->getStoreId()));
        $DigestMethod='MD5';
        $CalculateMethod=100;
        $SignatureValue=md5(preg_replace('/\s/is','', $Payload).
                            Mage::getStoreConfig('payment/buckaroo/soap_signature', Mage::app()->getStore()->getStoreId()));
                        


$requestXML=<<<REQUEST
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <PaymentGuarantee xmlns="https://payment.buckaroo.nl/">
            <XMLMessage>
                 {$Payload}
            </XMLMessage>
            <XMLSignature>
                <Signature xmlns="">
                     <Fingerprint>{$Fingerprint}</Fingerprint>
                     <DigestMethod>{$DigestMethod}</DigestMethod>
                     <CalculateMethod>{$CalculateMethod}</CalculateMethod>
                     <SignatureValue>{$SignatureValue}</SignatureValue>
                </Signature>
            </XMLSignature>
        </PaymentGuarantee>
    </soap:Body>
</soap:Envelope>
REQUEST;

//echo "<pre>".htmlentities($requestXML)."</pre>"; exit;

        $header = array("Content-type: text/xml");
        
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://payment.buckaroo.nl/soap/soap.asmx');
        curl_setopt($ch, CURLOPT_TIMEOUT, 900);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXML);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($ch);
        
        // Send debug message if needed
        $debug_email=Mage::getStoreConfig('payment/buckaroo/debug_email', Mage::app()->getStore()->getStoreId());
        
        if($debug_email!='')
        {
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->loadXML($result);
            $dom->formatOutput = true;
            $result=(string)$dom->saveXML();
            
            $debug_info='';
            $debug_info.="SOAP/XML request\n\n";
            $debug_info.="Query:\n";
            $debug_info.=$requestXML."\n";
            $debug_info.="-------------------------\n\n";
            $debug_info.="Answer:\n";
            $debug_info.=$result;
            
            mail($debug_email,"Buckaroo Payment service, debug e-mail", $debug_info);
        }
        // end of send debug
        
        // Parsing the result xml to get information from it
        $xml = new SimpleXMLElement($result);

        $ResponseStatus=$xml->children('soap', true)->children()->PaymentGuaranteeResponse->XMLMessage->Payload->Content->Transaction->StatusCode;
            
        $StatusDescription=$xml->children('soap', true)->children()->PaymentGuaranteeResponse->XMLMessage->Payload->Content->Transaction->StatusMessage;
    
        $AdditionalMessage="";
    
        return array($ResponseStatus, $StatusDescription, $AdditionalMessage, $order_id);   
    }

}