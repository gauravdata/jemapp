<?php
class Dealer4dealer_Syncinfo_Block_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Sales_Shipment_Grid
{
    protected function _prepareColumns()
    {
        $this->addColumn('exactonline_synced', array(
            'header'=> Mage::helper('sales')->__('Exact Online'),
            'width' => '40px',
            'type'  => 'number',
            'index' => 'exact_id',
            'renderer' => 'Dealer4dealer_Syncinfo_Block_Sales_Order_Grid',
            'sortable'  => false,
            'filter' => false
        ));

        return parent::_prepareColumns();
    }

    public function setCollection($collection)
    {
        // Get the name of the log table
        $logTable = Mage::getSingleton('core/resource')->getTableName('exactonline/log_shipment');

        $collection
            ->getSelect()
            ->joinLeft(array('log'=>$logTable),'main_table.entity_id = log.shipment_id',array('exact_id','state AS exact_sync_state','last_sync','status_message'));
        parent::setCollection($collection);
    }
}