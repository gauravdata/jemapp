<?php
class Dealer4dealer_Syncinfo_Block_Sales_Creditmemo_Column extends Mage_Adminhtml_Block_Sales_Creditmemo_Grid
{
    protected function _prepareColumns()
    {
    	$this->addColumn('exactonline_synced', array(
            'header'=> Mage::helper('sales')->__('Exact Online'),
            'width' => '40px',
            'type'  => 'number',
            'index' => 'exact_id',
            'renderer' => 'Dealer4dealer_Syncinfo_Block_Sales_Creditmemo_Grid',
            'sortable'  => true,
            'filter' => false
        ));

        return parent::_prepareColumns();
	}

    /**
     * Add Dealer4dealer exact online log
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        // Get the name of the log table
        $logTable = Mage::getSingleton('core/resource')->getTableName('exactonline/log_creditorder');

        $collection
            ->getSelect()
            ->joinLeft(array('log'=>$logTable),'main_table.entity_id = log.creditorder_id',array('exact_id','state AS exact_sync_state','last_sync','status_message'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}