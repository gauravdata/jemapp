<?php
class Total_Buckaroo_Model_Sources_States extends Varien_Object
{
    static public function toOptionArray()
    {
        $states=Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        
        $options=array();
        $options['']=Mage::helper('buckaroo')->__('-- Please Select --');
        
        foreach($states as $value=>$label)
        {
            $options[]=array('value'=>$label, 'label'=>$label);
        }
        
        return $options;
    }
}