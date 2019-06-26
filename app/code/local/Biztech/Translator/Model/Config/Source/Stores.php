<?php

class Biztech_Translator_Model_Config_Source_Stores extends Varien_Data_Collection
{

    public function toOptionArray()
    {
        $options = array();
        foreach (Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true) as $key => $value) {
            if ($key === 0) {
                continue;
            }
            $options[$key] = $value;
        }
        return $options;
    }

}