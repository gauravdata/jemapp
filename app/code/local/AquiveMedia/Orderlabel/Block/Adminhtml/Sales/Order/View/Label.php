<?php
/**
 * Author: Jeroen Smit - Smit Webdevelopment - www.smit-web.nl
 * Contact: jeroen@smit-web.nl
 * Copyright: Aquive Media
 * Created: 12/2/11
 */
class AquiveMedia_Orderlabel_Block_Adminhtml_Sales_Order_View_Label extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    public function getShippingAddress(){
        return $this->getOrder()->getShippingAddress();
    }

    public function getShippingAddressJSON(){
        $address = Mage::helper('orderlabel')->filterAddress($this->getShippingAddress());
        return Mage::helper('core')->jsonEncode($address);
    }
}
