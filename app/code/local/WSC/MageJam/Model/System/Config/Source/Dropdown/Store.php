<?php

class WSC_MageJam_Model_System_Config_Source_Dropdown_Store
{
    public function toOptionArray()
    {
        if (Mage::app()->isSingleStoreMode()) {
            return array(
                array(
                    'value' => Mage::app()->getStore(true)->getId(),
                    'label' => Mage::app()->getStore(true)->getName(),
                ),
            );
        } else {
            return Mage::getSingleton('adminhtml/system_store')
                ->getStoreValuesForForm(false, false);
        }
    }
}