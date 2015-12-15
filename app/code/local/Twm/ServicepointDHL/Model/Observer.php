<?php

class Twm_ServicepointDHL_Model_Observer extends Mage_Sales_Model_Quote_Address
{

    public function __construct()
    {

    }

    public function saveShippingMethod($evt)
    {
        $shippingMethodChoice = $evt->getRequest()->getParam('shipping_method', false);
        if (strpos($shippingMethodChoice,'servicepointdhl') !== false) {
            $code = explode('_', $shippingMethodChoice, 2);
            $code = $code[1];

            //get shipping address via SOAP
            $model = Mage::getModel('servicepointdhl/carrier_shippingMethod');
            $dhlAddress = $model->getDHLAddress($code);
            if ($dhlAddress) {
                $quote = $evt->getQuote();
	        $dhlAddress = $dhlAddress['data']['items'][0];

                $quote->getShippingAddress()
                    ->setPrefix($code)
                    ->setFirstname('DHL servicepoint')
                    ->setLastname($dhlAddress['name'])
                    //->setCompany($dhlAddress['name'])
                    ->setStreet($dhlAddress['add'])
                    ->setPostcode($dhlAddress['zip'])
                    ->setCity($dhlAddress['city'])
                    ->setCountryId($dhlAddress['country'])
                    ->setTelephone('0900 222 21 20');
                    //->setCollectShippingRates(true);

                $quote->collectTotals()->save();
            }
        }
    }

}
