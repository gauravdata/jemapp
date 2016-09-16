<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('app/Mage.php');
Mage::app();

echo '<strong>D4D DEBUG MODE</strong><br /><br />';


if(isset($_GET['id']) || !empty($_GET['id'])) {
    $id = $_GET['id'];
}

$order = Mage::getModel('sales/order')->load($id);

Zend_Debug::dump($order->getData());

