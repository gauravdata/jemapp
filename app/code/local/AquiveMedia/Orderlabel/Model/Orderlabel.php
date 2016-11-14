<?php
/**
 * Author: Jeroen Smit - Smit Webdevelopment - www.smit-web.nl
 * Contact: jeroen@smit-web.nl
 * Copyright: Aquive Media
 * Created: 12/2/11
 */
class AquiveMedia_Orderlabel_Model_Orderlabel extends Varien_Object
{
    protected $_addresses;

    public function processAddressCollection($addressCollection)
    {
        $this->_processAddressCollection($addressCollection);
    }

    private final function _processAddressCollection($addressCollection)
    {
        $addresses = array();
        foreach($addressCollection->getItems() as $address){
            $addresses[] = Mage::helper('orderlabel')->filterAddress($address);
        }
        $this->_addresses = $addresses;
    }

    public function getAddresses()
    {
        return $this->_addresses;
    }
}
