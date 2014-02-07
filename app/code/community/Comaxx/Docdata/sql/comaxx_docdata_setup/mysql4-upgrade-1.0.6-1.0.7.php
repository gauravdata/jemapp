<?php
$this->startSetup();

$connection = $this->_connection;

$qouteAddressTable = $this->_qouteAddressTable;
$connection->addColumn($qouteAddressTable, 'docdata_extra_street', 'varchar(255) NULL');
$connection->addColumn($qouteAddressTable, 'docdata_extra_housenumber', 'varchar(255) NULL');
$connection->addColumn($qouteAddressTable, 'docdata_extra_housenumber_addition', 'varchar(255) NULL');
$connection->addColumn($qouteAddressTable, 'docdata_extra_telephone', 'varchar(255) NULL');

$orderAddressTable = $this->_orderAddressTable;
$connection->addColumn($orderAddressTable, 'docdata_extra_street', 'varchar(255) NULL');
$connection->addColumn($orderAddressTable, 'docdata_extra_housenumber', 'varchar(255) NULL');
$connection->addColumn($orderAddressTable, 'docdata_extra_housenumber_addition', 'varchar(255) NULL');
$connection->addColumn($orderAddressTable, 'docdata_extra_telephone', 'varchar(255) NULL');

$sql = '';

$tables = array($this->_orderTable, $this->_quoteTable, $this->_invoiceTable);
foreach($tables as $table) {
	$sql .= "
	ALTER TABLE $table CHANGE docdata_afterpay_tax_amount docdata_fee_tax_amount decimal(12,4);
	ALTER TABLE $table CHANGE docdata_afterpay_amount docdata_fee_amount decimal(12,4);
	";
}

$connection->query($sql);

$this->endSetup();