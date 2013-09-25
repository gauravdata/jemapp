<?php
/**
 * Author: Jeroen Smit - Smit Webdevelopment - www.smit-web.nl
 * Contact: jeroen@smit-web.nl
 * Copyright: Aquive Media
 * Created: 12/2/11
 */
class AquiveMedia_Orderlabel_Block_Adminhtml_Print extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_addresses;

    public function __construct(){
        parent::_construct();
    }

    public function _beforeToHtml()
    {
        $this->_loadAddresses();
    }

    /**
     * Retrieves the Orderlabel Singleton and stores the contained addresses in the $_addresses class-variable.
     */
    private function _loadAddresses()
    {
        $this->_addresses = Mage::getSingleton('orderlabel/orderlabel')->getAddresses();
    }

    /**
     * Returns the addresses in either as an array or a json string.
     * @param bool $json Indicates that the addresses will be returned in a json string
     * @return mixed
     */
    public function getAddresses($json = false)
    {
        if ($json === true) {
            return Mage::helper('core')->jsonEncode($this->_addresses);
        } else {
            return $this->_addresses;
        }
    }

    /**
     * Returns the page title
     * @return string
     */
    public function getPageTitle()
    {
        return Mage::helper('orderlabel')->__('Order Labels Preview');
    }
}
