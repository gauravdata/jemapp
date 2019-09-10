<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('app/Mage.php');
Mage::app();


$order = Mage::getModel('sales/order')->load(500285083, 'increment_id');

$awObject = current($order->getAwStorecredit());

if($awObject && $awObject->getBaseStorecreditAmount()) {
	Zend_Debug::dump($awObject->getBaseStorecreditAmount());
}
