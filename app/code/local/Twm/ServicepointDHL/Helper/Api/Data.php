<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 12-10-15
 * Time: 14:44
 */ 
class Twm_ServicepointDHL_Helper_Api_Data extends PostcodeNl_Api_Helper_Data
{
    public function lookupAddress($postcode, $houseNumber, $houseNumberAddition)
    {
        Mage::getModel('core/session')->setLookupPostcode($postcode)
            ->setLookupHouseNumer($houseNumber);
        return parent::lookupAddress($postcode, $houseNumber, $houseNumberAddition);
    }
}