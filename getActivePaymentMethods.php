<?php
require_once 'app/Mage.php';
Mage::app();

$payments = Mage::getSingleton('payment/config')->getActiveMethods();
foreach($payments as $payment) {
    Zend_Debug::dump($payment->getData());
}