<?php
/**
 * Setup class for the resources to assist in the migration from the old plugin mostly
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Model_Resource_Setup extends Mage_Sales_Model_Mysql4_Setup {
	
	private $_oldPluginFilesToCheckCore = array(
		"app/etc/modules/Smile_Docdata.xml",
	);
	
	private $_oldPluginFilesToCheckAdditional = array(
		"skin/frontend/base/default/docdata/",
		"skin/adminhtml/default/default/images/docdata-payments.png",
		"skin/adminhtml/default/default/docdata.css",
		"js/docdata/",
		"docs/User_Guide_Smile_DocData_Module.pdf",
		"app/code/community/Smile/Docdata/",
		"app/design/frontend/base/default/template/docdata/",
		"app/design/frontend/base/default/layout/docdata.xml",
		"app/design/adminhtml/default/default/template/docdata/",
		"app/design/adminhtml/default/default/layout/docdata.xml"
	);
	
	/**
	 * Indicates if setup is in migration mode (convertion from old to new plugin)
	 */
	protected $_migrate;
	/**
	 * Varien_Db_Adapter_Pdo_Mysql connection to the database
	 */
	protected $_connection;
	/**
	 * List of tables to use in installation
	 */
	protected $_orderTable;
	protected $_quoteTable;
	protected $_orderGridTable;
	protected $_invoiceTable;
	protected $_configTable;
	protected $_resourceTable;
	protected $_qouteAddressTable;
	protected $_orderAddressTable;
	
	/**
	 * Creates Comaxx_Docdata_Resource_Setup object
	 *
	 * @param string $resourceName the setup resource name
	 *
	 * @return Comaxx_Docdata_Resource_Setup
	 */
	public function __construct($resourceName) {
		parent::__construct($resourceName);
		
		$this->_connection		= $this->getConnection();
		
		$this->_orderTable		= $this->getTable('sales/order');
		$this->_quoteTable		= $this->getTable('sales/quote');
		$this->_orderGridTable	= $this->getTable('sales/order_grid');
		$this->_invoiceTable	= $this->getTable('sales/invoice');
		$this->_configTable		= $this->getTable('core/config_data');
		$this->_resourceTable	= $this->getTable('core/resource');
		
		$this->_qouteAddressTable = $this->getTable('sales/quote_address');
		$this->_orderAddressTable = $this->getTable('sales/order_address');
		
	}
	
	/**
	 * Function to check if the old Smile_Docdata plugin was present in the system
	 *
	 * @return boolean|null Boolean which is true in case traces matching the old plugin were found
	 */
	private function _oldPluginExists($files, $is_blocker = true, $skip_remove = false) {
		$files_found = array();
		$base = Mage::getBaseDir();
		
		foreach ($files as $file) {
			$absolute_file = $base . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, explode('/', $file));
			
			if (file_exists($absolute_file)) {
				$files_found[$file] = $absolute_file;
			}
		}
		
		if (empty($files_found)) {
			// No files present so immediatly return false
			return false;
		}

		// At this point we know old files still exist
		// Some error messaging?
		$this->_reportPluginErrors('Old plugin files found.', $files_found);
		
		// For a recursive all prevent trying to remove everything
		if ($skip_remove) {
			return true;
		}
		
		// Lets try to remove them ourselves as a last resort
		$failed = $this->_rmPlugin($files_found);
		
		if (!empty($failed)) {
			// Some errors occured!
			$this->_reportPluginErrors('Could not remove files.', $failed, $is_blocker);
			return true;
		}
		
		return $this->_oldPluginExists($files, $is_blocker, true);
	}
	
	/**
	 * Recursive remove function with minimal error handling, returning the files/directories that couldn't be removed.
	 * 
	 * @param array $resources Array with strings representing absolute paths to a file or directory
	 *
	 * @return array An array with files or directories that couldn't be removed by the script
	 */
	private function _rmPlugin($resources) {
		$failed = array();
		
		foreach ($resources as $absolute) {
			$dir = is_dir($absolute);
			$contents_failed = array();
			
			if ($dir) {
				$contents = glob($absolute . '*', GLOB_MARK);
				$contents_failed = $this->_rmPlugin($contents);
				$failed = $failed + $contents_failed;
			}
			
			try {
				if ($dir) {
					// While checking wether the directory is empty (before trying to remove it) is possible,
					// it needs to fail in case it isn't so it is added to list of failures...
					if (!rmdir($absolute)) {
						$failed[] = $absolute;
					}
				} else {
					if (!unlink($absolute)) {
						$failed[] = $absolute;
					}
				}
			} catch(Exception $e) {
				$failed[] = $absolute;
			}
		}
		
		return $failed;
	}
	
	public function oldPluginResourceExists() {
		
		if(!isset($this->_migrate)) {
			$result = $this->_connection->query("SELECT version FROM `".$this->_resourceTable."` WHERE `code` = 'docdata_webmenu_setup'; ");
			$this->_migrate = ($result->rowCount() > 0) ? true : false;
		}
		return $this->_migrate;
	}
	
	/**
	 * Override for default applyUpdates to peform an extra check, preventing installation if it fails
	 *
	 * @return object Returns the instance of the current class
	 */
	public function applyUpdates() {
		$return = $this;
		// Prevent actual SQL upgrade if core file of old plugin still exists
		if(!$this->_oldPluginExists($this->_oldPluginFilesToCheckCore, true)) {
			// Try to remove all the other files as well since removing the core worked..
			// Failure here doesn't have as much of an impact so the return doesn't matter, failure will be logged
			$this->_oldPluginExists($this->_oldPluginFilesToCheckAdditional, false);
			$return = parent::applyUpdates();
		}
		return $return;
	}
	
	/**
	 * Override for default applyDataUpdates to peform an extra check, preventing installation if it fails
	 *
	 * @return object Returns the instance of the current class
	 */
	public function applyDataUpdates() {
		$return = $this;
		// Prevent version update in core_resource
		if(!$this->_oldPluginExists($this->_oldPluginFilesToCheckCore, true)) {
			// Try to remove all the other files as well since removing the core worked..
			// Failure here doesn't have as much of an impact so the return doesn't matter, failure will be logged
			$this->_oldPluginExists($this->_oldPluginFilesToCheckAdditional, false);
			$return = parent::applyDataUpdates();
		}
		return $return;
	}
	
	/**
	 * Retrieves list of present configuration rows for the old plugin
	 *
	 * @return array Results of search
	 */
	public function getConfigurationFields() {
		//make list of fields to convert
		$fields = array(
			'docdata/global/moduleMode',
			'docdata/global/webmenuType',
			'docdata/merchantAccount/username',
			'docdata/merchantAccount/password',
			'docdata/merchantAccount/testpassword',
			'docdata/paymentPreferences/profile',
			'docdata/paymentPreferences/numberOfDaysToPay',
			'docdata/paymentPreferences/exhortation/period1/numberOfDays',
			'docdata/paymentPreferences/exhortation/period1/profile',
			'docdata/paymentPreferences/exhortation/period2/numberOfDays',
			'docdata/paymentPreferences/exhortation/period2/profile',
			'docdata/menuPreferences/css/id',
			'payment/docdata_payments/title',
		);
		
		$sql = "SELECT * FROM `" . $this->_configTable . "` WHERE `path` IN ('";
		$sql.= implode("', '", $fields);
		$sql.= "')";
		return $this->_connection->query($sql)->fetchAll();
	}
	
	/**
	 * Builds sql query to perform an update with. Updates old config row to new row if present.
	 *
	 * @param array $config_result List of items to convert
	 *
	 * @return string Queries that update the config table
	 */
	public function buildConfigurationUpdate($config_result) {
		//build config entries to update
		$update_sql = '';
		$configTable = $this->_configTable;
		foreach($config_result as $config_entry) {
			switch($config_entry['path']) {
			case 'docdata/global/moduleMode':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/general/module_mode', ";
				$update_sql .= "value = '".strtolower($config_entry['value'])."' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/global/webmenuType':
				$value = 0;
				//determine type 
				if($config_entry['value'] === 'indirect') {
					$value = 1;
				}
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/general/webmenu_active', ";
				$update_sql .= "value = '".$value."' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/merchantAccount/username':
				//username needs to be set for production and test (old plugin shared the two)
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/merchant_account/production_username' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				//create new entry to test
				$update_sql .= "INSERT `".$configTable."` (`config_id`, `scope`, `scope_id`, `path`, `value`) VALUES (";
				$update_sql .= "NULL, '".$config_entry['scope']."', '".$config_entry['scope_id']."', 'docdata/merchant_account/test_username', '".$config_entry['value']."'); ";
				break;
			case 'docdata/merchantAccount/password':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/merchant_account/production_password' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/merchantAccount/testpassword':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/merchant_account/test_password' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/paymentPreferences/profile':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/payment_preferences/profile' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/paymentPreferences/numberOfDaysToPay':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/payment_preferences/number_of_days_to_pay' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/paymentPreferences/exhortation/period1/numberOfDays':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/payment_preferences/exhortation_period1_number_days' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/paymentPreferences/exhortation/period1/profile':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/payment_preferences/exhortation_period1_profile' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/paymentPreferences/exhortation/period2/numberOfDays':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/payment_preferences/exhortation_period2_number_days' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/paymentPreferences/exhortation/period2/profile':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/payment_preferences/exhortation_period2_profile' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'docdata/menuPreferences/css/id':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/general/webmenu_css_id' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			case 'payment/docdata_payments/title':
				$update_sql .= "UPDATE `".$configTable."` SET `path` = 'docdata/general/docdata_payment_title' ";
				$update_sql .= "WHERE `config_id` =".$config_entry['config_id']."; ";
				break;
			}
		}
		return $update_sql;
	}
	
	/**
	 * Builds sql query to set custom statuses to the old plugins status. 
	 *
	 * @return string Query that sets custom statuses
	 */
	public function buildStatusConfig() {
		$update_sql = "INSERT INTO `".$this->_configTable."` (`config_id`, `scope`, `scope_id`, `path`, `value`) VALUES ";
		$update_sql .= "(NULL, 'default', '0', 'docdata/custom_statuses/new', 'new_docdata'), ";
		$update_sql .= "(NULL, 'default', '0', 'docdata/custom_statuses/pending_payment', 'started_docdata'), ";
		$update_sql .= "(NULL, 'default', '0', 'docdata/custom_statuses/pending_refund', 'started_docdata'), ";
		$update_sql .= "(NULL, 'default', '0', 'docdata/custom_statuses/paid', 'paid_docdata'), ";
		$update_sql .= "(NULL, 'default', '0', 'docdata/custom_statuses/refunded', 'closed_refunded_docdata'), ";
		$update_sql .= "(NULL, 'default', '0', 'docdata/custom_statuses/charged_back', 'chargedback_docdata'), ";
		$update_sql .= "(NULL, 'default', '0', 'docdata/custom_statuses/canceled', 'canceled_docdata'), ";
		$update_sql .= "(NULL, 'default', '0', 'docdata/custom_statuses/on_hold', 'on_hold_docdata') ";
		$update_sql .= "; ";
		return $update_sql;
	}
	
	/**
	 * Builds table for locking mechanism 
	 *
	 * @return Object Table object for locking
	 */
	public function getLockTable($tableName) {
		$table = $this->_connection->newTable($tableName);
		$table->addColumn(
			'lock_code',
			Varien_Db_Ddl_Table::TYPE_TEXT,
			100,
			array(
				'nullable' => false,
				'primary' => true
			),
			'Lock Code'
		);
		$table->addColumn(
			'process_code',
			Varien_Db_Ddl_Table::TYPE_TEXT,
			100,
			array(
				'nullable' => true,
				'default' => null
			),
			'Process Code'
		);
		$table->addIndex('process_code', array('process_code'));
		$table->addColumn(
			'lock_time',
			Varien_Db_Ddl_Table::TYPE_INTEGER,
			null,
			array(
				'nullable' => false
			),
			'Lock Time'
		);
		$table->addIndex('lock_time', array('lock_time'));
		
		$table->setComment('For locking Docdata transactions');
		return $table;
	}
	
	/**
	 * Makes sure the column is present. In case old plugin exists the previous column is renamed to keep old data. 
	 *
	 * @param string $table Name of the target table
	 * @param string $oldColumn Name of the old column to migrate if present
	 * @param string #newColumn Name of the new column
	 * @param string $definition Definition of the column to be used in creating
	 *
	 * @return void
	 */
	public function AddOrChangeColumn($table, $oldColumn, $newColumn, $definition) {
		$connection = $this->_connection;
		if($this->oldPluginResourceExists() && $connection->tableColumnExists($table, $oldColumn)) {
			$connection->changeColumn($table, $oldColumn, $newColumn, $definition);
		} else {
			$connection->addColumn($table, $newColumn, $definition);
		}
	}
	
	private function _reportPluginErrors($msg, $files, $is_blocker = null) {
		// Presence of old plugin must always be logged
		Mage::log("Docdata Migration Impeded: \r\n" .$msg.' '.implode("\r\n", $files), Zend_Log::ERR, 'docdata.log', true);
		
		if ($is_blocker !== null) {
			$is_blocker = $is_blocker ? 'Stopped' : 'Error';
			$sql = "SELECT * FROM `adminnotification_inbox` WHERE `title` LIKE 'Docdata Plugin Migration $is_blocker' AND `is_read` = 0 AND `is_remove` = 0";
			$existing = $this->_connection->query($sql)->fetchAll();
			if(count($existing) === 0) {
				// Severe problem generates a message in the backend
				$this->run("
					INSERT INTO `adminnotification_inbox` (`severity`, `title`, `description`)
					VALUES (1, 'Docdata Plugin Migration $is_blocker', '" . $msg . "<br>\r\n" . implode("<br>\r\n", $files) . "')
				");
			}
		}
	}
}