<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 22-2-2017
 * Time: 21:37
 */ 
class Twm_Changegrid_Block_Adminhtml_Sales_Order_Grid extends Dealer4dealer_Syncinfo_Block_Sales_Order_Column {
    public function setCollection($collection)
    {
        $resource = Mage::getSingleton('core/resource');
        //sales/order_grid_collection
        /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $collection */
        $collection->getSelect()
            ->joinLeft(
            array('s' => $resource->getTableName('sales/order_address')),
            '`main_table`.entity_id = s.parent_id AND s.address_type = \'shipping\'',
            array('city')
        );

        return parent::setCollection($collection);
    }
}
