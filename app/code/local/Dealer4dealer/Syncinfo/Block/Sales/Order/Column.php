<?php
class Dealer4dealer_Syncinfo_Block_Sales_Order_Column extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareColumns()
    {
    	$this->addColumn('exactonline_synced', array(
            'header'=> Mage::helper('sales')->__('Exact Online'),
            'width' => '40px',
            'type'  => 'number',
            'index' => 'exact_id',
            'renderer' => 'Dealer4dealer_Syncinfo_Block_Sales_Order_Grid',
            'sortable'  => true,
            'filter' => false
        ));

        // ENABLE WHEN DELIVERY MODULE IS USED
        // $this->addColumn('exactonline_delivered', array(
        //     'header'=> Mage::helper('sales')->__('Exact Delivery'),
        //     'width' => '40px',
        //     'type'  => 'text',
        //     'index' => 'exactonline_id',
        //     'renderer' => 'Dealer4dealer_Syncinfo_Block_Sales_Order_Delivery',
        //     'filter'    => false,
        //     'sortable'  => false
        // ));

        return parent::_prepareColumns();
	}

    public $_hasJoinedExactLog = false;

    public function setCollection($collection)
    {
		if (!$this->_hasJoinedExactLog) {
				// Get the name of the log table
				$logTable = Mage::getSingleton('core/resource')->getTableName('exactonline/log_order');

				$collection
					->getSelect()
					->joinLeft(array('exact_log'=>$logTable),'main_table.entity_id = exact_log.order_id',array('exact_id','state AS exact_sync_state','last_sync','status_message'));
			$this->_haveJoinedExactLog = true;
		}
        parent::setCollection($collection);
    }
	
	
}