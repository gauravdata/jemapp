<?php
/**
 * My own options
 *
 */
class Kiyoh_Customerreview_Adminhtml_Model_System_Config_Source_Orderstatus
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
        foreach($data as $key => $item){
            $data[$key]['value'] = $item['status'];
        }
        return $data;
    }

}
?>