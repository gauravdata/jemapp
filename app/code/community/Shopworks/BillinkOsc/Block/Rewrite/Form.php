<?php

class Shopworks_BillinkOsc_Block_Rewrite_Form extends Shopworks_Billink_Block_Form
{

    /**
     * @var Shopworks_BillinkOsc_Helper_Data
     */
    private $_oscHelper;

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_oscHelper = Mage::helper('billink_osc');
    }

    /**
     * Indiciates if the delivery address fields should be displayed on the billink payment form
     * return bool
     */
    public function showDeliveryAddressFields()
    {
        //For OSC, always return the shipping address fields and hide or show them with javascript
        if($this->_oscHelper->isOscEnabled())
        {
            return $this->_helper->isAlternateDeliveryAddressAllowed();
        }
        else
        {
            return parent::showDeliveryAddressFields();
        }
    }
}