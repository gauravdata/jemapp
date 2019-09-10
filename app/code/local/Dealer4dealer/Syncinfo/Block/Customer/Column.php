<?php
class Dealer4dealer_Syncinfo_Block_Customer_Column extends Mage_Adminhtml_Block_Customer_Grid
{
    protected function _prepareColumns()
    {
    	$this->addColumn('exactonline_synced', array(
            'header'=> Mage::helper('customer')->__('Exact Online'),
            'width' => '40px',
            'type'  => 'text',
            'index' => 'exact_sync_state',
            'renderer' => 'Dealer4dealer_Syncinfo_Block_Customer_Grid',
            'filter'    => false,
            'sortable'  => true
        ));

        return parent::_prepareColumns();
	}

    public function setCollection($collection)
    {
        // Get the name of the log table
        $logTable = Mage::getSingleton('core/resource')->getTableName('exactonline/log_customer');

        $collection
            ->getSelect()
            ->joinLeft(array('log'=>$logTable),'e.entity_id = log.customer_id',array('state AS exact_sync_state','last_sync','status_message'));
        parent::setCollection($collection);
    }
}