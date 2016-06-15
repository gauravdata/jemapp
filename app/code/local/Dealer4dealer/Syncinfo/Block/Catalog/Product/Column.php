<?php
class Dealer4dealer_Syncinfo_Block_Catalog_Product_Column extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    protected function _prepareColumns()
    {
    	$this->addColumn('exactonline_synced', array(
            'header'=> Mage::helper('sales')->__('Exact Online'),
            'width' => '40px',
            'type'  => 'text',
            'index' => 'exactonline_id',
            'renderer' => 'Dealer4dealer_Syncinfo_Block_Catalog_Product_Grid',
            'filter'    => false,
            'sortable'  => true
        ));

        return parent::_prepareColumns();
	}

    public function setCollection($collection)
    {
        // Get the name of the log table
        $logTable = Mage::getSingleton('core/resource')->getTableName('exactonline/log_product');

        $collection
            ->getSelect()
            ->joinLeft(array('log'=>$logTable),'e.entity_id = log.product_id',array('state AS exact_sync_state','last_sync','status_message'));
        parent::setCollection($collection);
    }
}