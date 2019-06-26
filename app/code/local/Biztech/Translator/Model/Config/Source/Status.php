<?php

class Biztech_Translator_Model_Config_Source_Status extends Varien_Data_Collection
{

    public function toOptionArray()
    {
        $options = array(
            'success' => Mage::helper('translator')->__('Success'),
            'pending' => Mage::helper('translator')->__('Pending'),
            'abort' => Mage::helper('translator')->__('Aborted by Administrator'),
            'abort1' => Mage::helper('translator')->__('Aborted During Cron Process'),
        );

        return $options;
    }

}