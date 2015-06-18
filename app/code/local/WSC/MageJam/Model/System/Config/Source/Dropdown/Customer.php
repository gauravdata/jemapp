<?php

class WSC_MageJam_Model_System_Config_Source_Dropdown_Customer
{
    public function toOptionArray()
    {
        $customers = mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('id')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email')
            ->addAttributeToSort('created_at', 'DESC')
            ->addAttributeToSort('firstname')
            ->addAttributeToSort('lastname')
            ->addAttributeToSort('email');

        $customers->getSelect()->limit(100);

        $options = array();
        foreach ($customers as $customer){
            $option = array();
            $option['value'] = $customer->getId();
            $option['label'] = $customer->getFirstname().' '.$customer->getLastname().'/'.$customer->getEmail();
            $options[] = $option;
        }

        return $options;
    }
}