<?php

// Make absolutely sure we have the prerequisite
require_once BP . DS . 'app/code/core/Mage/Paypal/Model/Standard.php';

class Mage_Pap_Model_Paypal extends Mage_Paypal_Model_Standard
{
    public function ipnPostSubmit()
    {
      // Give PAP access to the data so it can handle any needed commissions
      try // If "Something Bad" happens, it isn't worth dying over
      {
        $papModel = Mage::getModel('pap/pap');
        $postData = $this->getIpnFormData();
        $customdata = explode('~~~a469ccb0-767c-4fed-96de-99427e1783aa~~~', $postData['custom']);
        $saleData = isset($customdata[0]) ? $customdata[0] : null;
        $cookieValue = isset($customdata[1]) ? $customdata[1] : null;
        
        // Convert the JSON data into a usable array
        $saleData = json_decode($saleData);
        $saleData = $this->ForceArray($saleData);
        
        $sReq = '';
        foreach($this->getIpnFormData() as $k=>$v) {
            $sReq .= '&'.$k.'='.urlencode(stripslashes($v));
        }
        //append ipn commdn
        $sReq .= "&cmd=_notify-validate";
        $sReq = substr($sReq, 1);

        $http = new Varien_Http_Adapter_Curl();
        $http->write(Zend_Http_Client::POST,$this->getPaypalUrl(), '1.1', array(), $sReq);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if ($response=='VERIFIED')
        {
          $papModel->registerSaleDetails($saleData, isset($cookieValue) ? $cookieValue : null);
        }
      }
      catch (Exception $e)
      {
        Mage::log('Caught exception while trying to log PayPal sale: '.$e->getMessage()."\n");
      }
      
      // let the base class go on to do whatever processing it was going to do
      parent::ipnPostSubmit();
    }
    
    // recursively force objects to arrays
    protected function ForceArray($data)
    {
      if (is_object($data))
      {
        $data = (array)$data;
      }
      if (is_array($data))
      {
        foreach ($data as $key=>$val)
        {
          $data[$key] = $this->ForceArray($val);
        }
      }
      return $data;
    }
}
