<?php
/**
 * @copyright   Dealer4Dealer
 */
class Dealer4dealer_Exactonline_Model_Sync_Exact_Stock extends Mage_Core_Model_Abstract
{
    private $log;
    private $xmlTool;
    private $exactConnector;
    private $settings;

    protected $_skuCache = array();
    protected $_attributeCache = array();
    protected $_warehouses = null;

    public function __construct()
    {
        $this->log              = Mage::getSingleton('exactonline/tools_log');
        $this->xmlTool          = Mage::getSingleton('exactonline/tools_xml_tool');
        $this->exactConnector   = Mage::getSingleton('exactonline/tools_connector');
        $this->settings         = Mage::getSingleton('exactonline/tools_settings');

        parent::__construct();
    }

    /**
     *  Download stock positions from Exact Online
     *  and save the positions in Magento.
     */
    public function synchronizeStock()
    {

        $xml = $this->getStockXml();

        $warehouses = explode(",", $this->settings->getSetting('product_warehouse_codes'));

        if (isset($xml->StockPositions)) {

            $stockPositions = array();

            /**
             *  Create array with all stock positions per warehouse
             *  and save the position of each warehouse to a custom
             *  attribute of the product.
             */
            foreach($xml->StockPositions->StockPosition as $stockPosition) {

                $sku        = (string) $stockPosition->Item['code'];
                $warehouse  = (string) $stockPosition->Warehouse['code'];

                if(in_array($warehouse, $warehouses)) {
                    $productId  = $this->_getProductId($sku);

                    if($productId) {
                        /**
                         *  Calculate the quantity. Do not add planning in since
                         *  we don't know the delivery time.
                         *
                         *  @todo Create setting to add planning in.
                         */
                        $currentQty = (int) $stockPosition->CurrentQuantity;
                        $out        = (int) $stockPosition->{'Planning-Out'};
                        $qty        = ($currentQty - $out);

                        $this->log->writeLog('Product ' . $sku . ' in warehouse '.$warehouse . ': '.$qty);

                        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                        $stockItem->setData('qty', $qty);

                        if($qty > 0) {
                            $stockItem->setData('is_in_stock', 1);
                        }

                        try {
                            $stockItem->save();
                            $this->log->writeLog('Saving product ID ' . $productId . ' with qty of ' .$qty);
                        } catch (Exception $e) {

                        }

                    } else {
                        $this->log->writeLog('Product '.$sku.' does not exists or is from the wrong warehouse.');
                    }
                } else {
                    $this->log->writeLog('Stock Update '.$sku.' is from the wrong warehouse: '.$warehouse);
                }

            }
        }

        $this->log->writeLog('Stock sync completed');
    }

    /**
     * Get a product id based on a SKU.
     *
     * @param string $sku
     * @return int
     */
    protected function _getProductId($sku)
    {
        if(in_array($sku, $this->_skuCache)) {
            return $this->_skuCache[$sku];
        }

        $productId  = Mage::getModel('catalog/product')->getIdBySku($sku);
        $this->_skuCache[$sku] = $productId;

        return $productId;
    }

    /**
     * Get the magento warehouse code from
     * the setting based on the warehouse code
     * from Exact Online.
     *
     * @param string $warehouse
     * @return string
     */
    protected function _getWarehouseCode($warehouse)
    {
        if(in_array($warehouse, $this->_attributeCache)) {
            return $this->_attributeCache[$warehouse];
        }

        $warehouseCode = $this->settings->getSetting('warehouse_'.$warehouse);
        $this->_attributeCache[$warehouse] = $warehouseCode;

        return $warehouseCode;
    }

    /**
     * Get the stock positions of all warehouses
     * of the product.
     *
     * @param int $productId
     * @return int
     */
    protected function _getStockValues($productId)
    {
        $collection = Mage::getModel('catalog/product')->getCollection();

        foreach($this->_getWarehouses() as $warehouseAttribute) {
            $collection->addAttributeToSelect($warehouseAttribute);
            $collection->addAttributeToSelect('stock_correction');
        }

        $product = $collection
            ->addAttributeToFilter('entity_id', $productId)
            ->getFirstItem();

        $qty = 0;
        foreach($this->_getWarehouses() as $warehouseAttribute) {
            $qty = $qty + (int) $product->getData($warehouseAttribute);
        }

        // Adjust stock position
        $qty = $qty - (int) $product->getStockCorrection();

        return $qty;
    }

    /**
     * Get all available warehouse attributes.
     *
     * @return array
     */
    protected function _getWarehouses()
    {
        if(is_null($this->_warehouses)) {
            $warehouses = $this->settings->getSetting('warehouse_codes');
            $warehouses = explode(',', $warehouses);

            $this->_warehouses = $warehouses;
        }

        return $this->_warehouses;
    }

    /**
     * Download new Stock Positions from Exact using
     * the build in paginator of Exact Online.
     *
     * @return SimpleXMLExtended
     */
    private function getStockXml()
    {
        $xmlResult = $this->xmlTool->strToXml($this->exactConnector->getStockXML());

        return $xmlResult;
    }
}