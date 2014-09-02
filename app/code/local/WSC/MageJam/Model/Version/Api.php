<?php

class WSC_MageJam_Model_Version_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Used for api call magejamVersion(), return current version of this module
     *
     * @return string
     */
    public function magejamVersion()
    {
        /* @var $helper WSC_MageJam_Helper_Data */
        $helper = Mage::helper('magejam');
        return $helper->getMagejamVersion();
    }
}