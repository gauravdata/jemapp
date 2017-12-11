<?php

error_reporting(-1);
ini_set('display_errors', 'On');

class Dealer4dealer_Exactonline_Model_Main
{
	private $settings;
	private $log;

	// Determines which modules to sync
	private $_enableProducts            = true;
	private $_enableCustomers           = true;
	private $_enableTransactions        = false;
	private $_enableSalesInvoices       = true; // ONLY CREDITS!!
	private $_enableSalesOrders         = true;
	private $_enableDeliveries          = false;
	private $_enableStockSync           = true;

    // Exact > Magento
    private $_enableExactProductSync    = false;
    private $_enableExactCustomerSync   = false; // LET OP!!!! in install SQL de code uit commentaar halen voor het aanmaken van het veld adressID in Magento!!!!
    private $_enablePriceListSync       = false;

	protected $_dateFormat = 'Y-n-j H:i:s';

	public function runUpdate()
	{
		$this->log      = Mage::getSingleton('exactonline/tools_log');
		$this->settings = Mage::getSingleton('exactonline/tools_settings');

        $this->_initSyncStatus();

        if($this->isReadyToGo()) {

            // @todo check for settings enable/disable etc. Create install script.
            $this->settings->saveSetting("running_since",date($this->_dateFormat));

            // Lock the synchronisation to avoid double synchronisations
            $this->setLock("lock");

            // Get the last synchronisation dates
            $lastSyncdateProduct     = $this->settings->getSetting("productsyncdate");
            $lastSyncdateCustomer    = $this->settings->getSetting("customersyncdate");
            $lastSyncdateOrder       = $this->settings->getSetting("ordersyncdate");
            $lastSyncdateCreditOrder = $this->settings->getSetting("creditordersyncdate");
            $lastSyncdateDelivery    = $this->settings->getSetting('deliverysyncdate');
            $lastSyncdatePricelist   = $this->settings->getSetting('priscelistsyncdate');
            $syncStartDate           = $this->settings->getSetting('sync_startdate');

            $this->log->writeLog('Starting synchronisation');

            /**** Exact > Magento Product Sync ****/
            if($this->_enableExactProductSync) {
                $exactProductSynchronize = Mage::getModel('exactonline/sync_exact_product');
                $exactProductSynchronize->runUpdate();
            }

            if (!$this->canContinue()) {
                return false;
            }

            /***** Exact > Magento Customer Sync ****/
            if ($this->_enableExactCustomerSync) {
                 $customerSync = Mage::getModel('exactonline/sync_exact_customer');
                 $customerSync->runUpdate();
            }

            if (!$this->canContinue()) {
                return false;
            }

            /********* PRODUCTS ********/
            if($this->_enableProducts) {
                $this->log->writeLog('Starting product synchronisation','Products');

                // Create filters to build a collection of products to sync.
                $filter = array('type_id' => array('in'=>array('simple','bundle')));

                // Start synchronisation
                $magentoProduct = Mage::getModel('exactonline/sync_magento_product');
                if($magentoProduct->synchronizeProducts($filter,$lastSyncdateProduct)) {
                    $this->log->writeLog('Product synchronisation finished without error','Products');
                }else {
                    $this->log->writeLog('Product synchronisation finished with errrors','Products');
                }
            }
            /****** END PRODUCTS ******/

            if (!$this->canContinue()) {
                return false;
            }

            /********* CUSTOMERS *******/
            if($this->_enableCustomers) {
                $this->log->writeLog('Starting customer synchronisation','Customers');

                // Create filters to build a collection of customers to sync
                $customGroups = explode(',', $this->settings->getSetting('not_sync_customer_groups'));
                $filter = array('group_id' => array('nin' => $customGroups));

                $magentoCustomer = Mage::getModel('exactonline/sync_magento_customer');
                if($magentoCustomer->synchronizeCustomers($filter,$lastSyncdateCustomer)) {
                    $this->log->writeLog('Customer synchronisation finished without error','Customers');
                }else {
                    $this->log->writeLog('Customer synchronisation finished with errrors','Customers');
                }
            }
            /****** END CUSTOMERS ******/

            if (!$this->canContinue()) {
                return false;
            }

            /********* VERKOOPBOEKINGEN *******/
            if($this->_enableTransactions) {
                $this->log->writeLog('Starting transaction synchronisation','Transactions');

                // Create filters to build a collection of orders to sync
                $filter = array(
                    'updated_at'=>array('from'=>$lastSyncdateOrder),
                    'status'=> array('in'=>explode(',',$this->settings->getSetting('order_sync_status')))
                );

                $magentoTransaction = Mage::getModel('exactonline/sync_magento_transaction');
                if($magentoTransaction->synchronizeTransactions($filter, $lastSyncdateOrder)) {
                    $this->log->writeLog('Transaction synchronisation finished without errors','Transactions');
                }else {
                    $this->log->writeLog('Transaction synchronisation finished with errors','Transactions');
                }

                // Credit transactions
                $this->log->writeLog('Starting Credit Invoice synchronisation','CreditInvoice');

                // Create filter to build collection of credits to sync
                $filter = array(
                    'updated_at' => array('from' => $lastSyncdateCreditOrder),
                    'invoice_id' => array('null' => true));
                    // TWM: Only send invoice credits, skip others.

                $magentoTransactionCredit = Mage::getModel('exactonline/sync_magento_transaction_credit');
                if($magentoTransactionCredit->synchronizeCreditTransactions($filter)) {
                    $this->log->writeLog('CreditTransction synchronisation finished without errors','CreditTransaction');
                }else {
                    $this->log->writeLog('CreditTransction synchronisation finished with errors','CreditTransaction');
                }
            }

            /******* END VERKOOPBOEKINGEN *****/

            if (!$this->canContinue()) {
                return false;
            }

            /********* VERKOOPFACTUUR *******/
            if($this->_enableSalesInvoices) {
/*                $this->log->writeLog('Starting Invoices synchronisation','Invoices');

                // Create filters to buid a collection of orders to sync
                $filter = array('updated_at' => array('from'=>$lastSyncdateOrder),'status' => array('in' => explode(',',$this->settings->getSetting('order_sync_status'))));

                // Build XML and send to Exact Online
                $magentoOrder = Mage::getModel('exactonline/sync_magento_invoice');
                if($magentoOrder->synchronizeInvoices($filter,$lastSyncdateOrder)) {
                    $this->log->writeLog('Invoice synchronisation finished without errors','Invoices');
                }else {
                    $this->log->writeLog('Invoice synchronisation finished with errors','Invoices');
                }*/

                $this->log->writeLog('Starting Credit Invoice synchronisation','CreditInvoice');

                // Create filter to build collection of credits to sync
                $filter = array(
                    'created_at' => array('from'=>$syncStartDate),
                    'updated_at'=>array('from'=>$lastSyncdateCreditOrder));

                $magentoInvoiceCredit = Mage::getModel('exactonline/sync_magento_invoice_credit');
                if($magentoInvoiceCredit->synchronizeCreditInvoices($filter, $lastSyncdateCreditOrder)) {
                    $this->log->writeLog('CreditInvoice synchronisation finished without errors','CreditInvoice');
                }else {
                    $this->log->writeLog('CreditInvoice synchronisation finished with errors','CreditInvoice');
                }
            }
            /******** END VERKOOPFACTUUR ****/

            if (!$this->canContinue()) {
                return false;
            }

            /********* VERKOOP ORDER *******/
            if($this->_enableSalesOrders) {

                $this->log->writeLog('Startig SalesOrders synchronisation','SalesOrders');

                $filter = array(
                    'created_at' => array('from'=>$syncStartDate),
                    'updated_at' => array('from'=>$lastSyncdateOrder),'status' => array('in' => explode(',',$this->settings->getSetting('order_sync_status'))));

                $magentoOrder = Mage::getModel('exactonline/sync_magento_order');
                if($magentoOrder->synchronizeOrders($filter,$lastSyncdateOrder)) {
                    $this->log->writeLog('SalesOrder synchronisation finished without errors','SalesOrders');
                }else {
                    $this->log->writeLog('SalesOrder synchronisation finished with errors','SalesOrders');
                }
            }
            /******** END VERKOOP ORDER ****/

            if (!$this->canContinue()) {
                return false;
            }

            /********* LEVERINGEN *******/
            if($this->_enableDeliveries) {

                $this->log->writeLog('Starting Delivery synchronisation','Deliveries');
                $filter = array('updated_at' => array('from'=>$lastSyncdateDelivery));

                $magentoOrder = Mage::getModel('exactonline/sync_magento_delivery');
                if($magentoOrder->syncDeliveries($filter)) {
                    $this->log->writeLog('Delivery synchronisation finished without errors','Deliveries');
                }else {
                    $this->log->writeLog('Delivery synchronisation finished with errors','Deliveries');
                }
            }

            if (!$this->canContinue()) {
                return false;
            }

            if($this->_enableStockSync) {
                $exactStock = Mage::getModel('exactonline/sync_exact_stock');
                $exactStock->synchronizeStock();
            }

            if (!$this->canContinue()) {
                return false;
            }

            /********* PRIJSLIJSTEN *******/
            if ($this->_enablePriceListSync) {
                $listSync = Mage::getModel('exactonline/sync_magento_price_list');
                $listSync->runUpdate($lastSyncdatePricelist);
            }
            /******** END PRIJSLIJSTEN ****/

            $this->setLock("unlock");
        } else {
            throw new Exception(Mage::helper('exactonline')->__('Could not start the module. The module is locked or turned off.'));
        }
	}

    /**
     * Check if the connector is enabled and unlocked. When an error occurs during the sync
     * the connector stays locked. We check how long the connector is locked and unlock it
     * when 3 hours have passed.
     *
     * @return bool
     */
	private function isReadyToGo()
	{
		// Check if the module is disabled
		$moduleEnabled = ($this->settings->getSetting("link_enabled") == 'true') ? true : false;

        if(!$moduleEnabled){
			$this->log->writeLog('Synchronisation is disabled. Use link_enabled to activate the module');
			return false;
		}

		// Check if the module is currently running or is locked
		$locked = $this->settings->getSetting('lock') == '1' ? true : false;

		if($locked) {
			//$runningSince   = strtotime($this->settings->getSetting('running_since'));
            $runningSince   = strtotime($this->settings->getSetting('last_heartbeat'));
			$currentTime    = strtotime(date($this->_dateFormat));
			$lockedSince    = abs($currentTime-$runningSince);

			if($lockedSince > 900) {
				$this->setLock('unlock');
				$this->log->writeLog('No heartbeat detected for 15 minutes, unlocking module.');
				return true;
			}else {
				$this->log->writeLog('Module is locked. Synchronisation not started.');
				return false;
			}
		}else {
			return true;
		}
	}

	private function setLock($lock)
	{
		switch($lock) {
			case 'lock':
				$this->settings->saveSetting('lock',1);
				break;
			case 'unlock':
			$this->settings->saveSetting('lock', 0);
			break;
		}
	}

    protected function _isDebugMode()
    {
        $debugMode = $this->settings->getSetting('debug_mode');

        if($debugMode == '1') {
            return true;
        }

        return false;
    }

    protected function _initSyncStatus()
    {
        if ($this->settings->getSetting('enable_products') == '0') {
            $this->_enableProducts = false;
            $this->_enableExactProductSync = false;
        }

        if ($this->settings->getSetting('enable_customers') == '0') {
            $this->_enableCustomers = false;
            $this->_enableExactCustomerSync = false;
        }

        if ($this->settings->getSetting('enable_orders') == '0') {
            $this->_enableTransactions = false;
            $this->_enableSalesOrders = false;
        }

        if ($this->settings->getSetting('enable_credits') == '0') {
            $this->_enableSalesInvoices = false;
        }

        if ($this->settings->getSetting('enable_delivery') == '0') {
            $this->_enableDeliveries = false;
        }

        if ($this->settings->getSetting('enable_stock') == '0') {
            $this->_enableStockSync = false;
        }

        if ($this->settings->getSetting('enable_pricelist') == '0') {
            $this->_enablePriceListSync = false;
        }
    }

    public function canContinue()
    {
        $tool = Mage::getSingleton('exactonline/tools_tooling');
        if(!$tool->canContinue($this->settings->getSetting("running_since"), $this->settings->getSetting("max_script_runtime"))) {
            $this->log->writeLog('Maximum time exceeded after customer sync. Aborting synchronisation.');
            $this->setLock("unlock");
            return false;
        }

        return true;
    }

}