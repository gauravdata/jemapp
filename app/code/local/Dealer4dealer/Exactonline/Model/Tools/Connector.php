<?php
class Dealer4dealer_Exactonline_Model_Tools_Connector extends Mage_Core_Model_Abstract
{
    private static $_connector_instance;

    private $log;
    private $partnerKey;
    private $settings;
    private $division;
    private $xmlAnalizer;
    private $utilities;

    protected $_maxLifetime = 600;
    protected $_tokenTime = 0;

    protected $_apiClientId;

    public static function getInstance()
    {
        if (!isset(self::$_connector_instance)) {
            $class = __CLASS__;
            self::$_connector_instance = new $class;
        }

        return self::$_connector_instance;
    }

    public function __construct()
    {
        $this->settings         = Mage::getSingleton('exactonline/tools_settings');
        $this->log              = Mage::getSingleton('exactonline/tools_log');
        $this->xmlAnalizer      = Mage::getSingleton('exactonline/tools_xml_analyzer');
        $this->utilities        = Mage::getSingleton('exactonline/tools_utilities');

        $this->_apiClientId     = $this->settings->getSetting('api_client_id');
        $this->partnerKey       = $this->settings->getSetting("partnerkey");
        $this->division         = $this->settings->getSetting('exact_division');

        parent::__construct();
    }

    private function initCurl($url)
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $this->refreshTokens();

        $header = array();
        $header[] = 'X-ExactOnline-ApplicationKey: ' . $this->_apiClientId;
        $header[] = 'content-type: application/json';
        $header[] = 'accept: application/json';
        $header[] = 'Authorization: Bearer ' . base64_decode(Mage::helper('core')->decrypt($this->settings->getSetting('access_token')));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        return $ch;
    }

    /**
     * @param $type
     * @param $xml
     * @return array
     */
    public function sendXML($type, $xml)
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $url = sprintf('https://start.exactonline.nl/docs/XMLUpload.aspx?Topic=%s&_Division_=%s&PartnerKey=%s', $type, $this->division, urlencode($this->_apiClientId));

        try {
            $ch = $this->initCurl($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

            $response = curl_exec($ch);

            if ($response === false) {
                $error = curl_error($ch);
                $this->log->writeLog('Error while sending XML for ' . $type. ' Error: ' . $error);
            } else {
                $this->log->writeLog('XML send successfully for ' . $type);
            }

            return array('result' => $response, 'ch' => $ch);

        } catch (Exception $e) {
            $this->log->writeLog('Error while sending XML for ' . $type. ' Error: ' . $e->getMessage(). ' Aborting synchronisation.');
            curl_close($ch);
            die('Aborting Synchronisation');
        }
    }

    public function setDivision($divisionNr)
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $this->division = $divisionNr;

        $this->log->writeLog('Switching Division to ' . $divisionNr);
    }

    public function refreshTokens()
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        if ((time() - $this->_tokenTime) > $this->_maxLifetime) {

            $this->log->writeLog('AccessToken expired. Refreshing oAuth2 Token');

            $client = new Zend_Http_Client();

            $refreshToken   = base64_decode(Mage::helper('core')->decrypt($this->settings->getSetting('refresh_token')));
            $userId         = $this->settings->getSetting('api_user_id');
            $apiKey         = $this->settings->getSetting('api_key');
            $apiKey         = base64_decode(Mage::helper('core')->decrypt($apiKey));
            $country        = $this->settings->getSetting('api_country');

            $url = $this->settings->getSetting('api_url');
            $url = $url . '/refresh.php?refresh_token=%s&connector_id=%s&api_key=%s&country=%s';
            $url = sprintf($url, urlencode($refreshToken), urlencode($userId), urlencode($apiKey), urlencode($country));

            $client->setUri($url);
            $client->setMethod('POST');
            $response = $client->request();

            if ($response->isSuccessful()) {

                $data = $response->getBody();

                $tokens = Zend_Json::decode($data);

                $this->settings->saveSetting('access_token', Mage::helper('core')->encrypt(base64_encode($tokens['access_token'])));
                $this->settings->saveSetting('refresh_token', Mage::helper('core')->encrypt(base64_encode($tokens['refresh_token'])));

                $this->_tokenTime = time();
            } else {

                $this->log->writeLog('Error while refreshing AccessToken. Aborting Synchronisation.');
                die('Aborting Synchronisation');
            }
        } else {
            $this->log->writeLog('AccessToken is still valid. Do not  refresh oAuth2 tokens');
        }
    }

    public function getStockXML()
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $this->log->writeLog('Starting Stock synchronisation');

        $downloadId = $this->settings->getSetting('download_id_stock');
        if(is_null($downloadId) || $downloadId == '') {
            $downloadId = 'D4D';
        }

        $url = "https://start.exactonline.nl/docs/XMLDownload.aspx?Topic=StockPositions&PartnerKey=".$this->partnerKey."&Params_DownloadID=".$downloadId.'&_Division_='.$this->division; //.'&username='.$this->_username.'&password='.$this->_password;

        $ch = $this->initCurl($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode($url));

        $result = curl_exec($ch);

        if (curl_error($ch) != '') {
            $this->log->writeLog('Error while sending Stock XML');
            return false;
        } else {
            $this->log->writeLog('Stock XML received');
            return $result;
        }
    }

    public function getProducts()
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $this->log->writeLog('Start downloading Product XML');

        $downloadId = $this->settings->getSetting('download_id');
        if(is_null($downloadId) || $downloadId == '') {
            $downloadId = 'D4D';
        }

        $url = sprintf('https://start.exactonline.nl/docs/XMLDownload.aspx?Topic=Items&PartnerKey=%s&Params_DownloadID=%s&_Division_=%s', $this->_apiClientId, $downloadId, $this->division);

        $ch = $this->initCurl($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode($url));
        $result = curl_exec($ch);

        if(curl_error($ch) != '') {
            $this->log->writeLog('Error while sending Product XML');
            return false;
        } else {
            $this->log->writeLog('Product XML received.');
            return $result;
        }
    }

    public function getCustomers()
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $this->log->writeLog('Start downloading Customer XML');

        $downloadId = $this->settings->getSetting('download_id');
        if(is_null($downloadId) || $downloadId == '') {
            $downloadId = 'D4D';
        }

        $url = sprintf('https://start.exactonline.nl/docs/XMLDownload.aspx?Topic=Accounts&PartnerKey=%s&Params_DownloadID=%s&Params_ValidEmailAddress=%s&_Division_=%s', $this->_apiClientId, $downloadId, 1, $this->division);

        $ch = $this->initCurl($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode($url));
        $result = curl_exec($ch);

        if(curl_error($ch) != '') {
            $this->log->writeLog('Error while sending Product XML');
            return false;
        } else {
            $this->log->writeLog('Customers XML received.');
            return $result;
        }
    }

    /**
     * @deprecated Used in older versions of the connector to check if orders has been synchronised.
     * @return mixed|string
     */
    public function getOrderNumbersXML()
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $this->log->writeLog('Starting downloading Order XML');

        $downloadId = $this->settings->getSetting('download_id');
        if(is_null($downloadId) || $downloadId == '') {
            $downloadId = 'D4D';
        }

        $url = 'https://start.exactonline.nl/docs/XMLDownload.aspx?Topic=SalesOrders&PartnerKey='.$this->partnerKey.'&_Division_='. $this->division.'&Params_DownloadID='.$downloadId;

        $ch = $this->initCurl($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode($url));
        $result = curl_exec($ch);

        if(curl_error($ch) != '') {
            $this->log->writeLog('Error while downloading Order XML');

            return false;

        } else {
            $this->log->writeLog('Order XML received');

            return $result;
        }
    }

    public function findDebtor($emailAddress)
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $this->log->writeLog('Looking for debtor with email address ' . $emailAddress);

        try {

            $this->refreshTokens();

            $contentType = 'application/json';

            $client = new Zend_Http_Client();
            $client->setHeaders(array(
                'X-ExactOnline-PartnerKey' => $this->_apiClientId,
                'Content-Type' => $contentType,
                'Accept' => $contentType,
                'Authorization: Bearer ' . base64_decode(Mage::helper('core')->decrypt($this->settings->getSetting('access_token')))
            ));

            $url = sprintf('https://start.exactonline.nl//api/v1/%s/crm/Accounts?$filter=Email+eq+%s&$select=ID,Code,Email', $this->division, "'".$emailAddress."'");

            $client->setUri($url);
            $response = $client->request('GET');

            if ($response->isSuccessful()) {

                $body = $response->getBody();
                $data = Zend_Json::decode($body);

                if (isset($data['d']['results'])) {
                    return array_shift($data['d']['results']);
                }

                $this->log->writeLog('No debtor found with email address ' . $emailAddress);
            }
            else
            {
                $this->log->writeLog('No Valid response for account: ' . $emailAddress);
                $this->log->writeLog('HTTP-Status: ' . $response->getStatus() );
                $this->log->writeLog('HTTP-Message: ' . $response->getMessage() );
            }

        } catch (Exception $e) {
            $this->log->writeLog('Error while looking for debtor with email address ' . $emailAddress. ': ' . $e->getMessage());
        }

        return false;
    }

    public function getCustomerByDebtorId($debtorId)
    {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        $url = sprintf('https://start.exactonline.nl/docs/XMLDownload.aspx?Topic=Accounts&PartnerKey=%s&Params_Code=%s&_Division_=%s',$this->_apiClientId,$debtorId, $this->division);

        $ch = $this->initCurl($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode($url));

        $response = curl_exec($ch);

        if (curl_errno($ch) != '') {
            $this->log->writeLog('Error while downloading customer information.');
            return false;
        } else {
            return simplexml_load_string($response);
        }
    }

    public function getPriceLists($lastSyncDate){
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        try {

            $this->refreshTokens();

            $url = sprintf('https://start.exactonline.nl/api/v1/%s/sales/PriceLists?$select=%s&$orderby=%s&$filter=Modified+gt+DateTime\'%s\'', $this->division, 'Code,ID,Modified', 'Modified', urlencode($lastSyncDate));

            $client = new Zend_Http_Client($url);

            $header = array(
                'X-ExactOnline-ApplicationKey'  => $this->_apiClientId,
                'content-type'                  => 'application/json',
                'accept'                        => 'application/json',
                'Authorization'                 => 'Bearer ' . base64_decode(Mage::helper('core')->decrypt($this->settings->getSetting('access_token')))
            );

            $client->setHeaders($header);

            $response = $client->request('GET');

            if ($response->isSuccessful()) {

                $body = $response->getBody();

                $data = Zend_Json::decode($body);

                return $data['d']['results'];
            }
        } catch (Exception $e) {
            $this->log->writeLog('Error while getting pricelists: ' .  $e->getMessage());
        }

        return array();
    }

    public function getPricelist($priceListGuid) {
        $this->settings->saveSetting('last_heartbeat', date('Y-m-d H:i:s'));
        try {
            $client = new Zend_Http_Client();

            $this->refreshTokens();

            $header = array(
                'X-ExactOnline-ApplicationKey' => $this->_apiClientId,
                'content-type' => 'application/json',
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . base64_decode(Mage::helper('core')->decrypt($this->settings->getSetting('access_token')))
            );

            $client->setHeaders($header);

            $url = sprintf('https://start.exactonline.nl/docs/SlsPriceListSearch.aspx?Export=1&_Division_=%s&PriceList=%s&SysExporting=4&csvdelimiter=%s&exportlines=%', $this->division, urlencode($priceListGuid), urlencode(';'), 0);
            $client->setUri($url);

            $response = $client->request('POST');

            if ($response->isSuccessful()) {
                return $response->getBody();
            }
        } catch (Exception $e) {
            $this->log->writeLog('Error while getting pricelist: ' .  $e->getMessage());
        }

        return false;
    }
}