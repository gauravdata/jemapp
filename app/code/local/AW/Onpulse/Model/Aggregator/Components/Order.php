<?php

class AW_Onpulse_Model_Aggregator_Components_Order extends AW_Onpulse_Model_Aggregator_Component
{
    const COUNT_CUSTOMERS = 5;

    public function getCollectionForOldMegento()
    {
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
        ;
        $collection = $this->_addAddressFields($collection);
        if (!method_exists(get_class($collection), 'addExpressionFieldToSelect')) {
            $collection
                ->getSelect()
                ->columns(array('billing_name' => 'CONCAT(billing_o_a.firstname, " ",billing_o_a.lastname)'))
                ->columns(array('shipping_name' => 'CONCAT(shipping_o_a.firstname, " ",shipping_o_a.lastname)'))
            ;
        } else {
            $collection
                ->_addExpressionFieldToSelect('billing_name',
                    'CONCAT({{billing_firstname}}, " ", {{billing_lastname}})',
                    array('billing_firstname', 'billing_lastname')
                )
                ->_addExpressionFieldToSelect('shipping_name',
                    'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                    array('shipping_firstname', 'shipping_lastname')
                )
            ;
        }
        return $collection;
    }

    public function pushData($event = null){
        if(version_compare(Mage::getVersion(),'1.4.1.0','<=')) {
            $orderCollection=$this->getCollectionForOldMegento();
            $orderCollection
                ->addOrder('entity_id','DESC')
                ->setPageSize(self::COUNT_CUSTOMERS)
            ;
        } else {
            $orderCollection = Mage::getModel('sales/order')->getCollection()
                ->addAddressFields()
                ->addAttributeToSelect('*')
                ->addOrder('entity_id','DESC')
                ->setPageSize(self::COUNT_CUSTOMERS);
        }
        $aggregator = $event->getEvent()->getAggregator();
        $aggregator->setData('orders', $orderCollection->load());
    }

    protected function _addAddressFields($collection)
    {
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';
        $joinTable = $collection->getTable('sales/order_address');
        $collection
            ->getSelect()
            ->joinLeft(
                array($billingAliasName => $joinTable),
                "(main_table.entity_id = $billingAliasName.parent_id AND $billingAliasName.address_type = 'billing')",
                array(
                     $billingAliasName . '.firstname',
                     $billingAliasName . '.lastname',
                     $billingAliasName . '.telephone',
                     $billingAliasName . '.postcode'
                )
            )
            ->joinLeft(
                array($shippingAliasName => $joinTable),
                "(main_table.entity_id = $shippingAliasName.parent_id AND $shippingAliasName.address_type = 'shipping')",
                array(
                     $shippingAliasName . '.firstname',
                     $shippingAliasName . '.lastname',
                     $shippingAliasName . '.telephone',
                     $shippingAliasName . '.postcode'
                )
            )
        ;
        return $collection;
    }
}