<?php


$installer = $this;
$installer->startSetup();

$resource = Mage::getSingleton('core/resource');
$tableName = $resource->getTableName('core/email_template');
$db = $resource->getConnection('core_write');

$orderId = Mage::getModel('sales/order')->getCollection()->setPageSize(1)->getFirstItem()->getId();
$invoiceId = Mage::getModel('sales/order_invoice')->getCollection()->setPageSize(1)->getFirstItem()->getId();
$shipmentId = Mage::getModel('sales/order_shipment')->getCollection()->setPageSize(1)->getFirstItem()->getId();
$creditMemoId = Mage::getModel('sales/order_creditmemo')->getCollection()->setPageSize(1)->getFirstItem()->getId();

if ($orderId)
    Mage::getConfig()->saveConfig('mtemail/preview/order_id',       $orderId,       'default', 0);

if ($invoiceId)
    Mage::getConfig()->saveConfig('mtemail/preview/invoice_id',     $invoiceId,     'default', 0);

if ($shipmentId)
    Mage::getConfig()->saveConfig('mtemail/preview/creditmemo_id',  $shipmentId,    'default', 0);

if ($creditMemoId)
    Mage::getConfig()->saveConfig('mtemail/preview/shipment_id',    $creditMemoId,  'default', 0);


$installer->endSetup();