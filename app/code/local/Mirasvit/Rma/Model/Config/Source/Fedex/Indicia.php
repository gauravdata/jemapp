<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   2.4.0
 * @build     1607
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rma_Model_Config_Source_Fedex_Indicia
{
    /**
     * Constructs shipment methods list for RMA FedEx Config
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            Mirasvit_Rma_Model_Config::FEDEX_SMARTPOST_INDICIA_MEDIAMAIL => Mage::helper('rma')->__('Media Mail'),
            Mirasvit_Rma_Model_Config::FEDEX_SMARTPOST_INDICIA_PARCEL => Mage::helper('rma')->__('Parcel Select'),
            Mirasvit_Rma_Model_Config::FEDEX_SMARTPOST_INDICIA_PRESORTED =>
                Mage::helper('rma')->__('Presorted Standard'),
            Mirasvit_Rma_Model_Config::FEDEX_SMARTPOST_INDICIA_PRESORTED_BOUND =>
                Mage::helper('rma')->__('Presorted Bound Printed Matter'),
            Mirasvit_Rma_Model_Config::FEDEX_SMARTPOST_INDICIA_PARCEL_RETURN =>
                Mage::helper('rma')->__('Parcel Return'),
        );
    }

    /**
     * Constructs option list for RMA FedEx Config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        foreach ($this->toArray() as $k => $v) {
            $result[] = array('value' => $k, 'label' => $v);
        }

        return $result;
    }
}
