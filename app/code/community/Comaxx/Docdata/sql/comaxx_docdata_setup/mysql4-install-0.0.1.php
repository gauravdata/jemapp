<?php
// Crude, but better than trying hoplessly to get stuff working
// while the only thing that is missing is a proper php version!
if (version_compare(phpversion(), '5.3.10', '<')) {
	// php version isn't high enough
	throw new ErrorException("Sorry! But the PHP version on this server is not supported by the Comaxx Docdata plugin. Minimum required version is 5.3.10");
}

$this->startSetup();

$connection = $this->_connection;

$migrate = $this->oldPluginResourceExists();

if($migrate) {
	//remove resource entries of old plugin
	$sql = "DELETE FROM `".$this->_resourceTable."` ";
	$sql .= "WHERE `code` = 'docdata_rowlock_setup' OR `code` = 'docdata_webmenu_setup'; ";
	$connection->query($sql);
}

/*******************************************************************************************/
/************* Begin Adding/migrating columns **********************************************/
/*******************************************************************************************/
//add required columns on order table
$orderTable = $this->_orderTable;
$connection->addColumn($orderTable, 'docdata_payment_id', 'BIGINT NULL');
$connection->addColumn($orderTable, 'docdata_afterpay_tax_amount', 'decimal(12,4) NULL');
//in case of migration check if old column is present and rename it
$this->AddOrChangeColumn($orderTable, 'docdata_cluster_key', 'docdata_payment_order_key', 'varchar(64) NULL');
$this->AddOrChangeColumn($orderTable, 'docdata_afterpay_cost', 'docdata_afterpay_amount', 'decimal(12,4) NULL');

//add required columns on quote table
$quoteTable = $this->_quoteTable;
$connection->addColumn($quoteTable, 'docdata_afterpay_tax_amount', 'decimal(12,4) NULL');
//in case of migration check if old column is present and rename it
$this->AddOrChangeColumn($quoteTable, 'docdata_afterpay_cost', 'docdata_afterpay_amount', 'decimal(12,4) NULL');

//add required column on order grid table
$connection->addColumn($this->_orderGridTable, 'docdata_payment_id', 'BIGINT NULL');

//add required columns on invoice table
$invoiceTable = $this->_invoiceTable;
$connection->addColumn($invoiceTable, 'docdata_afterpay_tax_amount', 'decimal(12,4) NULL');
//in case of migration check if old column is present and rename it
$this->AddOrChangeColumn($invoiceTable, 'docdata_afterpay_cost', 'docdata_afterpay_amount', 'decimal(12,4) NULL');

/*******************************************************************************************/
/************** Begin Configuration migration **********************************************/
/*******************************************************************************************/
if($migrate) {
	//Extract convert configuration fields
	$update_sql = $this->buildConfigurationUpdate($this->getConfigurationFields());
	
	//disable payment methods query (default disabled) (also reset to docdata payment method choice)
	$update_sql .= "DELETE FROM `".$this->_configTable."` ";
	$update_sql .= "WHERE `path` like 'payment/docdata_%/active' or `path` = 'docdata/general/webmenu_active'; ";
	
	//enable plugin using docdata payment option
	$update_sql .= "INSERT INTO `".$this->_configTable."` (`config_id`, `scope`, `scope_id`, `path`, `value`) VALUES ";
	$update_sql .= "(NULL, 'default', '0', 'docdata/general/active', '1'), ";
	$update_sql .= "(NULL, 'default', '0', 'docdata/general/webmenu_active', '1'); ";
	
	//delete old commands (not used anymore, cleanup)
	$update_sql .= "DELETE FROM `".$this->_configTable."` ";
	$update_sql .= "WHERE `path` like 'payment/docdata_%/command_%'; ";
	
	//set custom statuses to keep using the same status as previous version
	$update_sql .= $this->buildStatusConfig();
	
	//add migration date
	$update_sql .= "INSERT INTO `".$this->_configTable."` (`config_id`, `scope`, `scope_id`, `path`, `value`) VALUES ";
	$update_sql .= "(NULL, 'default', '0', 'docdata/general/migrated', LOCALTIME); ";
	
	$connection->query($update_sql);
}

/*******************************************************************************************/
/************** Add locking table if not present *******************************************/
/*******************************************************************************************/
$tableName = $this->getTable('docdata_lock');
if ( ! $connection->isTableExists($tableName)) {
	//get table and add it 
	$table = $this->getLockTable($tableName);
	$connection->createTable($table);
}

/*******************************************************************************************/
/************** Unsupported check **********************************************************/
/*******************************************************************************************/
//add unsupported version notice if needed (community versions 1.6.2 and 1.7 + enterprise version 1.12)
if ( version_compare(Mage::getVersion(), '1.6.2.0', '<')===true
	  || version_compare(Mage::getVersion(), '1.8.0', '>=')===true
	 && !version_compare(Mage::getVersion(), '1.12.0.0', '=')===true) {
	$this->run("
		INSERT INTO `adminnotification_inbox` (`severity`, `title`, `description`)
		VALUES (1, 'Unsupported Magento version', 'Current version of Magento is not supported by the Docdata plugin. Use of the Docdata plugin is at your own risk.')
	");
}

$this->endSetup();