<?php
/**
 * File used with shared functions and handling of the Klarna library
 *
 * PHP Version 5.3
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */

require_once Mage::getBaseDir('lib') .
    '/Klarna/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc';
require_once Mage::getBaseDir('lib') .
    '/Klarna/Klarna.php';
require_once Mage::getBaseDir('lib') .
    '/Klarna/pclasses/mysqlstorage.class.php';

//Klarna::$debug = true;

/**
 * API Helper extension
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Api extends Mage_Core_Helper_Abstract
{

    protected $klarna;
    protected $paymentmethod = '';

    /**
     * Check for available module updates
     *
     * @return bool
     */
    public function checkForUpdates()
    {
        try {
            $kURL = 'http://static.klarna.com/external/msbo/magento.latest.txt';
            $modules = Mage::getConfig()->getNode('modules')->children();
            $modulesArray = (array)$modules;
            $current = $modulesArray['Klarna_KlarnaPaymentModule']->version;
            $latest = file_get_contents($kURL);
            if (empty($latest)) {
                return false;
            }
            $imgPath = Mage::getBaseUrl(
                Mage_Core_Model_Store::URL_TYPE_SKIN,
                Mage::app()->getRequest()->isSecure()
            );
            $updateTemplate = new Mage_Core_Block_Template();
            $updateTemplate->setTemplate('klarna/update.phtml');
            $updateTemplate->assign('imgPath', $imgPath);
            $updateTemplate->assign('current', $current);
            $updateTemplate->assign('latest', $latest);
            $kittURL = Klarna_KlarnaPaymentModule_Helper_Checkout::STATIC_KITT;
            $updateTemplate->assign(
                "checkoutCSS", "{$kittURL}res/v1.1/checkout.css"
            );
            $updateTemplate->assign(
                'newAvailable', version_compare($latest, $current, '>')
            );
            return $updateTemplate->renderView();
        } catch(Exception $e) {
        }
        return false;
    }

    /**
     * Initialise the Api helper.
     */
    public function __construct()
    {
        // Setup static configuration of kitt
        $lib = Mage::getBaseDir('lib');
        KiTT::configure(
            array(
                'module' => 'Magento',
                'version' => '4.2.6',
                'paths' => array(
                    'extra_templates' => Mage::getBaseDir() .
                        '/skin/frontend/base/default/klarna/templates/',
                    'kitt' => "{$lib}/KiTT/",
                    'lang' => "{$lib}/KiTT/data/language.json",
                    'lookup' => "{$lib}/KiTT/data/lookupTable.json",
                    'input' => "{$lib}/KiTT/data/inputFields.json"
                ),
                'api' => array(
                    'pcStorage' => 'mysql',
                    'pcURI' => $this->getPCURI()
                )
            )
        );
    }

    /**
     * This methods load the correct config based on the store id
     *
     * @param int $storeId The storeid to configure for
     *
     * @return void
     */
    public function loadConfig($storeId = null)
    {
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $isSecure = Mage::app()->getRequest()->isSecure();
        KiTT::configure(
            array(
                'default' => Mage::getStoreConfig(
                    'general/country/default', $storeId
                ),
                'api/mode' => $this->getHost($storeId),
                'web' => array(
                    'ajax' => Mage::getBaseUrl(
                        Mage_Core_Model_Store::URL_TYPE_LINK, $isSecure
                    ) . 'klarna/address/dispatch',
                    'css' => Mage::getBaseUrl(
                        Mage_Core_Model_Store::URL_TYPE_SKIN, $isSecure
                    ) . '/frontend/base/default/klarna/',
                    'js' => Mage::getBaseUrl(
                        Mage_Core_Model_Store::URL_TYPE_JS, $isSecure
                    ),
                    'img' => Mage::getBaseUrl(
                        Mage_Core_Model_Store::URL_TYPE_SKIN, $isSecure
                    ) . '/frontend/base/default/klarna/',
                )
            )
        );

        $activatedCountries = Mage::helper('klarnaPaymentModule')
            ->getActivatedCountries($storeId);

        foreach ($activatedCountries as $country) {
            $this->_configureKlarna($country, $storeId);
        }
    }

    /**
     * Configure a Klarna objects and save them to KiTT
     *
     * @param string $country The country to configure
     * @param int    $storeId The storeid to configure for
     *
     * @return void
     */
    private function _configureKlarna($country, $storeId)
    {
        $country = strtolower($country);
        $merchantId = (int) Mage::getStoreConfig(
            "klarna/{$country}/merchant_id", $storeId
        );
        if ($merchantId === null || $merchantId <= 0) {
            return;
        }
        $sharedSecret = Mage::getStoreConfig(
            "klarna/{$country}/shared_secret", $storeId
        );
        if (strlen($sharedSecret) == 0) {
            return;
        }
        $locale = KiTT::locale($country);
        KiTT::configure(
            array(
                "sales_countries/{$locale->getCountryCode()}" => array(
                    "eid" => $merchantId,
                    "secret" => $sharedSecret
                )
            )
        );
    }

    /**
     * Build the PClass URI
     *
     * @return array
     */
    public function getPCURI()
    {
        $mageConfig = Mage::getResourceModel('sales/order')
            ->getReadConnection()->getConfig();
        return array(
            "user"      => $mageConfig['username'],
            "passwd"    => $mageConfig['password'],
            "dsn"       => $mageConfig['host'],
            "db"        => $mageConfig['dbname'],
            "table"     => "klarnapclasses"
        );
    }

    /**
     * Clear the pclasses table
     *
     * @return void
     */
    public function clearPClasses()
    {
        $pcstorage = new MySQLStorage();
        $pcstorage->clear($this->getPCURI());
    }

    /**
     * Translate the payment code into a KiTT code
     *
     * @param $string $paymentMethod The payment method to translate
     *
     * @return string
     */
    public function getPaymentCode($paymentMethod)
    {
        switch ($paymentMethod) {
        case 'klarna_partpayment':
            return KiTT::PART;
        case 'klarna_specpayment':
            return KiTT::SPEC;
        case 'klarna_invoice':
            return KiTT::INVOICE;
        default:
            throw new Exception("Invalid Klarna Solution");
        }
    }

    /**
     * Returns the correct host
     *
     * @param int $storeId The store view to use
     *
     * @return int
     */
    public function getHost($storeId)
    {
        $host = Mage::getStoreConfig('klarna/general/host', $storeId);
        return ($host === 'LIVE') ? Klarna::LIVE : Klarna::BETA;
    }

}
