<?php
/**
 * Author: Jeroen Smit - Smit Webdevelopment - www.smit-web.nl
 * Contact: jeroen@smit-web.nl
 * Copyright: Aquive Media
 * Created: 12/2/11
 */
class AquiveMedia_OrderLabel_Adminhtml_OrderlabelController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction(){
        $this->loadLayout();
    }

    public function indexAction(){
        $this->_initAction();

        $this->renderLayout();
    }

    public function massPrintAction(){
        $this->_initAction();
        $this->_title($this->__('Sales'))->_title($this->__('Label Printing'));
        $orderIds = $this->getRequest()->getParam('order_ids');
        $collection = Mage::getModel('sales/order_address')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('parent_id',array('in'=>$orderIds))
            ->addFieldToFilter('address_type',array('eq'=>'shipping'));

        Mage::getSingleton('orderlabel/orderlabel')->processAddressCollection($collection);
        $this->renderLayout();
    }
}
