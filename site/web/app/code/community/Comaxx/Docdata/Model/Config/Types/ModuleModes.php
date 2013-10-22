<?php
/**
 * Source model for module modes
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Model_Config_Types_ModuleModes
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'test', 'label' => Mage::helper('docdata')->__('Test Mode')),
            array('value' => 'production', 'label' => Mage::helper('docdata')->__('Production Mode')),
        );
    }
}