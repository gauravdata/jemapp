<?php
class Total_Buckaroo_Model_Sources_StatusesFailed extends Varien_Object
{
    static public function toOptionArray()
    {
        $state=Mage::getStoreConfig('payment/buckaroo/order_state_failed', Mage::app()->getStore()->getStoreId());
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