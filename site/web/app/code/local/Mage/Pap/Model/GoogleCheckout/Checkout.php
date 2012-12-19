<?php

// Make absolutely sure we have the prerequisite
require_once BP . DS . 'app/code/core/Mage/GoogleCheckout/Model/Api/Xml/Checkout.php';

class Mage_Pap_Model_GoogleCheckout_Checkout extends Mage_GoogleCheckout_Model_Api_Xml_Checkout
{
    protected function _getMerchantPrivateDataXml()
    {
        // except that getRequest is not defined for this class yet. Getting at the 
        $xml = <<<EOT
            <merchant-private-data>
                <quote-id><![CDATA[{$this->getQuote()->getId()}]]></quote-id>
                <pap-cookie-data><![CDATA[{$this->getPost('pap-cookie-data')}]]></pap-cookie-data>
                </merchant-private-data>
EOT;
        return $xml;
    }
    protected function getPost($val)
    {
       return Mage::app()->getRequest()->getPost($val);
    }
}
