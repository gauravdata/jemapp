<?php
class Total_BuckarooCollect_Model_Sources_Statuses601 extends Varien_Object
{
    static public function toOptionArray()
    {
        $state=Mage::getStoreConfig('payment/buckaroocollect/order_state_601', Mage::app()->getStore()->getStoreId());
        $statuses=Mage::getSingleton('sales/order_config')->getStateStatuses($state);
         
        $options=array();
        $options[]=array('value'=>'', 'label'=>Mage::helper('buckaroo')->__('-- Please Select --'));
        foreach($statuses as $value=>$label)
        {
            $options[]=array('value'=>$value, 'label'=>$label);
        }
        
        return $options;
    }
}