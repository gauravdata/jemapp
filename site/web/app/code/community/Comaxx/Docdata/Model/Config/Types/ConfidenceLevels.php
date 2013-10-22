<?php
/**
 * Source model for module modes
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Model_Config_Types_ConfidenceLevels
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'authorization', 'label' => Mage::helper('docdata')->__('Authorization')),
            array('value' => 'capture', 'label' => Mage::helper('docdata')->__('Capture')),
        );
    }
}