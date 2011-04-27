<?php
class Total_Buckaroo_Adminhtml_StatusesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $state=$this->getRequest()->getParam('state');
        
        $statuses=Mage::getSingleton('sales/order_config')->getStateStatuses($state);
        
        $options=array();
        $options[]=array('value'=>'', 'label'=>Mage::helper('buckaroo')->__('-- Please Select --'));
        
        foreach($statuses as $value=>$label)
        {
            $options[]=array('value'=>$value, 'label'=>$label);
        }
        
        echo json_encode($options); exit;
    }
}