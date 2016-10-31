<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('app/Mage.php');
Mage::app();

echo '<strong>D4D DEBUG MODE</strong><br /><br />';

function tr($title, $value, $style = '', $format = 4, $round = 2)
{
    if(is_float($value)) {
        $value = number_format(round($value, $round), $format);
    }

    echo '<div style="width:98%;height:2em;border-top:1px solid #efefef;line-height:2em;padding:0 0.4em;' . $style .'"><div style="float:left">' . $title . ':</div><div style="float:right">' . $value . '</div></div>';
}

if(isset($_GET['id']) || !empty($_GET['id'])) {
    $id = $_GET['id'];
}
$filter = array(
                        'created_at'=>array('from'=>'2016-02-01')
                    );

$orderCollection = Mage::getModel('sales/order')->getCollection()
                    ->addFieldToSelect('entity_id')
                    ->addFieldToSelect('increment_id');

                    while(list($key, $value) = each($filter)){
                $orderCollection->addAttributeToFilter($key, $value);
            }


                    foreach($orderCollection as $order) {
                    	$order->load($order->getId());
                    	echo $order->getRealOrderId().' '.$order->getPayment()->getMethod().'<br />';
                    }


