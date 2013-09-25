<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.\n
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.11
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_CPanel_Whm extends Gpf_Object {

    private $host;
    private $user;
    private $passwd;
    private $useSsl = true;
    private $port = 2087;

    function __construct($host, $user, $passwd, $useSsl = true, $port = 2087) {
        if (!strlen($host)) {
            throw new Gpf_Exception('Host has to be defined in WHM Server');
        }
        $this->host = $host;
        $this->user = $user;
        $this->passwd = $passwd;
        $this->useSsl = $useSsl;
        $this->port = $port;
    }

    /**
     * Execute request to CPanel
     *
     * @param $path CPanel command path
     * @param $params Array of parameters, which input to CPanel command
     * @return SimpleXMLElement
     */
    protected function execute($path, $params = array()) {
        $client = new Gpf_Net_Http_Client();
        $request = new Gpf_Net_Http_Request();
        $url = ($this->useSsl ? 'https://' : 'http://') . $this->host . ':' . $this->port . $path;
        Gpf_Log::info('Request URL: ' . $url);
        $query = '';
        foreach ($params as $name => $value) {
            $query .= '&' . $name . '=' . urlencode($value);
        }
        Gpf_Log::info('Request params: ' . $query);
        $request->setUrl($url . (strlen($query) ? '?' : '') . ltrim($query, '&'));
        $request->setHttpUser($this->user);
        $request->setHttpPassword($this->passwd);
        Gpf_Log::info("Executing HTTP request: " . $request->toString());
        return $this->parseResult($client->execute($request));
    }

    /**
     * Parse XML response
     *
     * @param Gpf_Net_Http_Response $response
     * @return array XML
     */
    protected function parseResult(Gpf_Net_Http_Response $response) {
        try {
            $errorMessage = '';
            $xml = @new SimpleXMLElement($response->getBody());

            if (((string)$xml->status) === '0' || ((string)$xml->result->status === '0')) {
                $errorMessage = strlen((string)$xml->statusmsg) ? (string)$xml->statusmsg : (string)$xml->result->statusmsg;
            }
            if (((string)$xml->data->result) === '0' && strlen((string)$xml->data->reason)) {
                $errorMessage = (string)$xml->data->reason;
            }
            if (strlen($errorMessage)) {
                throw new Gpf_Exception($errorMessage);
            }
        } catch (Exception $e) {
            throw new Gpf_Exception("Failed to execute CPanel command with error: " . $e->getMessage());
        }

        return $xml;
    }

    /**
     * List all accounts created on server matching selected search criteria
     *
     * @param $search Search criteria. (Perl Regular Expression)
     * @param $searchType Type of account search. (domain | owner | user | ip | package )
     * @return array of Gpf_CPanel_Account
     */
    public function listAccounts($search = '', $searchType = 'domain') {
        $xml = $this->execute('/xml-api/listaccts', array('searchtype' => $searchType, 'search' => $search));

        $arrAccounts = array();
        foreach ($xml->acct as $xmlAccount) {
            $cpAccount = new Gpf_CPanel_Account();
            $cpAccount->setDiskLimit((string) $xmlAccount->disklimit);
            $cpAccount->setDiskUsed((string) $xmlAccount->diskused);
            $cpAccount->setDomain((string) $xmlAccount->domain);
            $cpAccount->setEmail((string) $xmlAccount->email);
            $cpAccount->setIp((string) $xmlAccount->ip);
            $cpAccount->setOwner((string) $xmlAccount->owner);
            $cpAccount->setPartition((string) $xmlAccount->partition);
            $cpAccount->setPackage((string) $xmlAccount->plan);
            $cpAccount->setStartDate((string) $xmlAccount->startdate);
            $cpAccount->setSuspended((string) $xmlAccount->suspended);
            $cpAccount->setSuspendReason((string) $xmlAccount->suspendreason);
            $cpAccount->setTheme((string) $xmlAccount->theme);
            $cpAccount->setUnixStartDate((string) $xmlAccount->unix_startdate);
            $cpAccount->setUser((string) $xmlAccount->user);
            $arrAccounts[] = $cpAccount;
        }
        return $arrAccounts;
    }

    /**
     * Create new account on server
     *
     * @param $domain Account domain name - has to be unique on server
     * @param $userName Account user name - has to be unique
     * @param $password Account password
     * @param $package Package name for account
     * @param $contactEmail Contact email address
     * @param $quota in MB, 0 means unlimited
     * @param $bandwidthLimit in MB, 0 means unlimited
     * @param $ip Assign dedicated ip. Supported values: y/n
     * @param $cgi Has cgi. Supported values: y/n
     * @param $frontpage Support frontpage. Supported values: y/n
     * @param $hasshell Has shell access. Supported values: y/n
     * @param $cpTheme Cpanel theme name (e.g. x3)
     * @param $maxFtpAccounts Max. FTP accounts. 0 means unlimited
     * @param $maxSqlDatabases Max SQL databases. 0 means unlimited
     * @param $maxMailAccounts Max mail accounts. 0 means unlimited
     * @param $maxSubdomains Max sub domains. 0 means unlimited
     * @param $maxParkedDomains Max parked domains. 0 means unlimited
     * @param $maxAddonDomains Max addon domains. 0 means unlimited
     * @param $customIP Specify custom IP address
     * @return boolean If account created, return true, else generate exception with error message
     */
    public function createAccount($domain, $userName, $password, $package,
    $contactEmail, $customIP = false, $quota = false, $bandwidthLimit = false, $ip = 'n',
    $cgi = 'y', $frontpage = 'n', $hasshell = 'y',
    $cpTheme = 'x3', $maxFtpAccounts = false, $maxSqlDatabases = 10,
    $maxMailAccounts = 10, $maxSubdomains = 10, $maxParkedDomains = 10,
    $maxAddonDomains = 10) {
        $arr = array('username' => $userName,'domain' => $domain);

        $arr['plan'] = $package;
        $arr['password'] = $password;
        if ($quota !== false) $arr['quota'] = $quota;
        $arr['ip'] = $ip;
        $arr['cgi'] = $cgi;
        $arr['frontpage'] = $frontpage;
        $arr['hasshell'] = $hasshell;
        $arr['contactemail'] = $contactEmail;
        $arr['cpmod'] = $cpTheme;
        if ($maxFtpAccounts !== false) $arr['maxftp'] = $maxFtpAccounts;
        $arr['maxsql'] = $maxSqlDatabases;
        $arr['maxpop'] = $maxMailAccounts;
        $arr['maxsub'] = $maxSubdomains;
        $arr['maxpark'] = $maxParkedDomains;
        $arr['maxaddon'] = $maxAddonDomains;
        if ($bandwidthLimit !== false) $arr['bwlimit'] = $bandwidthLimit;
        if ($customIP !== false) $arr['customip'] = $customIP;


        $xml = $this->execute('/xml-api/createacct', $arr);

        return true;
    }


    /**
     * Suspend CPanel account
     *
     * @param $userName Username to suspend
     * @param $reason Reason of suspension
     * @return boolean Returns true if account was successfully suspended, otherwise throws exception
     */
    public function suspendAccount($userName, $reason) {
        $xml = $this->execute('/xml-api/suspendacct',
        array('user' => $userName,'reason' => $reason));

        return true;
    }


    /**
     * Unsuspend CPanel account
     *
     * @param $userName Username to unsuspend
     * @return boolean Returns true if account was successfully unsuspended, otherwise throws exception
     */
    public function unSuspendAccount($userName) {
        $xml = $this->execute('/xml-api/unsuspendacct', array('user' => $userName));

        return true;
    }


    /**
     * Terminate CPanel account
     *
     * @param $userName Username to terminate
     * @param $keepDNS Keep DNS entries for the domain (default is no, 1 | y = Yes, 0 | n = No, )
     * @return boolean Returns true if account was successfully unsuspended, otherwise throws exception
     */
    public function terminateAccount($userName, $keepDNS = 0) {
        $xml = $this->execute('/xml-api/removeacct', array('user' => $userName, 'keepdns' => $keepDNS));

        return true;
    }


    /**
     * Add dns record to server
     *
     * @param $domain Domain name to add
     * @param $ip IP address of new domain
     * @return boolean Returns true if domain was successfully added, otherwise throws exception
     */
    public function addDns($domain, $ip) {
        $xml = $this->execute('/xml-api/adddns', array('domain' => $domain, 'ip' => $ip));
        return true;
    }


    /**
     * Delete Dns entry from server
     *
     * @param $domain Domain to delte from DNS
     * @return boolean Returns true if domain was successfully deleted, otherwise throws exception
     */
    public function deleteDns($domain) {
        $xml = $this->execute('/xml-api/killdns', array('domain' => $domain));
        return true;
    }

    /**
     * Get List of DNS zones
     *
     * @return array Returns array of domains registerred on server
     */
    public function getDnsZones() {
        $xml = $this->execute('/xml-api/listzones');
        $arrZones = array();
        foreach ($xml->zone as $zone) {
            $arrZones[] = (string) $zone->domain;
        }
        return $arrZones;
    }


    /**
     * List One Zone — dumpzone
     * This function displays the DNS zone configuration for a specific domain.
     *
     * @return Gpf_Data_RecordSet Returns recordset of dns entry records
     */
    public function dumpZone($domain) {
        $xml = $this->execute('/xml-api/dumpzone', array('domain' => $domain));

        $records = new Gpf_Data_RecordSet();
        $records->setHeader(array(
        'name', 'Line', 'address', 'class', 'exchange', 'preference',
        'expire', 'minimum', 'mname', 'nsdname', 'cname', 'raw',
        'refresh', 'retry', 'rname', 'serial', 'ttl',
        'type', 'txtdata'));
        foreach ($xml->result->record as $record) {
            $dnsRecord = $records->createRecord();

            $dnsRecord->set('name', (string) $record->name);
            $dnsRecord->set('Line', (string) $record->Line);
            $dnsRecord->set('address', (string) $record->address);
            $dnsRecord->set('class', (string) $record->class);
            $dnsRecord->set('exchange', (string) $record->exchange);
            $dnsRecord->set('preference', (string) $record->preference);
            $dnsRecord->set('expire', (string) $record->expire);
            $dnsRecord->set('minimum', (string) $record->minimum);
            $dnsRecord->set('mname', (string) $record->mname);
            $dnsRecord->set('nsdname', (string) $record->nsdname);
            $dnsRecord->set('cname', (string) $record->cname);
            $dnsRecord->set('raw', (string) $record->raw);
            $dnsRecord->set('refresh', (string) $record->refresh);
            $dnsRecord->set('retry', (string) $record->retry);
            $dnsRecord->set('rname', (string) $record->rname);
            $dnsRecord->set('serial', (string) $record->serial);
            $dnsRecord->set('ttl', (string) $record->ttl);
            $dnsRecord->set('type', (string) $record->type);
            $dnsRecord->set('txtdata', (string) $record->txtdata);

            $records->addRecord($dnsRecord);
        }
        return $records;
    }

    /**
     * Edit Zone Record — editzonerecord
     *
     * @return array Returns array of domains registerred on server
     */
    public function editDnsZoneRecord($domain, $line, $address = false,
    $class = false, $cname= false, $exchange = false, $preference = false,
    $expire  = false, $minimum = false, $mname = false, $name = false,
    $nsdname = false, $raw = false, $refresh = false, $retry = false,
    $rname = false, $serial = false, $ttl = false, $type = false, $txtdata = false) {

        $params = array ('domain' => $domain, 'Line' => $line);
        if ($address !== false) $params['address'] = $address;
        if ($class !== false) $params['class'] = $class;
        if ($cname !== false) $params['cname'] = $cname;
        if ($exchange !== false) $params['exchange'] = $exchange;
        if ($preference !== false) $params['preference'] = $preference;
        if ($expire !== false) $params['expire'] = $expire;
        if ($minimum !== false) $params['minimum'] = $minimum;
        if ($mname !== false) $params['mname'] = $mname;
        if ($name !== false) $params['name'] = $name;
        if ($nsdname !== false) $params['nsdname'] = $nsdname;
        if ($raw !== false) $params['raw'] = $raw;
        if ($refresh !== false) $params['refresh'] = $refresh;
        if ($retry !== false) $params['retry'] = $retry;
        if ($rname !== false) $params['rname'] = $rname;
        if ($serial !== false) $params['serial'] = $serial;
        if ($ttl !== false) $params['ttl'] = $ttl;
        if ($type !== false) $params['type'] = $type;
        if ($txtdata !== false) $params['txtdata'] = $txtdata;

        $xml = $this->execute('/xml-api/editzonerecord', $params);

        return true;
    }


    /**
     * Change bandwidth limit of account and read bandwidth status of account
     *
     * @param $userName Name of user to modify the bandwidth usage (transfer) limit for.
     * @param $limit Bandwidth Usage (Transfer) Limit. (in MB)
     * @return Gpf_CPanel_AccountBandWidth
     */
    public function editBandWidthLimit($userName, $limit) {
        $xml = $this->execute('/xml-api/limitbw', array('user' => $userName, 'bwlimit' => $limit));
        $result = new Gpf_CPanel_AccountBandWidth();
        $result->setLimit((string)  $xml->result->bwlimit->bwlimit);
        $result->setLimitEnabled((string)  $xml->result->bwlimit->bwlimitenable);
        $result->setHumanLimit((string)  $xml->result->bwlimit->human_bwlimit);
        $result->setHumanLimitUsed((string)  $xml->result->bwlimit->human_bwused);
        $result->setUnlimited((string)  $xml->result->bwlimit->unlimited);
        return $result;
    }

    /**
     * Load Bandwidth Usage
     *
     * @param $month The month for which you wish to view the bandwidth usage. Possible values 1-12.
     * @param $year The year for which you wish to view bandwidth usage.
     * @param $resellerUsername The username of the reseller whose bandwidth information you wish to view.
     * @param $searchQuery A sequence of characters for which you would like to search.
     * @param $searchType Specifies what kind of information you wish to search. Ex. domain, user, owner, package
     * @return array of arrays [accountid=>(used,limit), accountid=>(used,limit)]
     */
    public function getBandWidthUsage($month = false, $year = false, $resellerUsername= false, $searchQuery=false, $searchType = "user") {
        $params = array();
        if ($month) $params['month'] = $month;
        if ($year) $params['year'] = $year;
        if ($resellerUsername) $params['showres'] = $resellerUsername;
        if ($searchQuery) $params['search'] = $searchQuery;
        if ($searchType) $params['searchtype'] = $searchType;
        $xml = $this->execute('/xml-api/showbw', $params);

        $result = array();

        foreach($xml->bandwidth->acct as $account) {
            $result[(string) $account->user] = array('used' => (string) $account->totalbytes, 'limit' => (string) $account->limit);
        }

        return $result;
    }


    /**
     * Change cpanel package
     *
     * @param $userName Name of user to modify the bandwidth usage (transfer) limit for.
     * @param $packageName New Package Name
     * @return boolean
     */
    public function changePackage($userName, $packageName) {
        $xml = $this->execute('/xml-api/changepackage', array('user' => $userName, 'pkg' => $packageName));
        return true;
    }

    /**
     * Get the Server's load average
     *
     * @param $timeRange Time range for which is returned server load. Possible values: 1, 5, 15
     * @return boolean
     */
    public function getServerLoad($timeRange = 1) {
        $xml = $this->execute('/xml-api/loadavg');
        switch ($timeRange) {
            case 1: return (string) $xml->one;
            case 5: return (string) $xml->five;
            case 15: return (string) $xml->fifteen;
            default: return (string) $xml->one;
        }
    }


    /**
     * List SSL Certificates — listcrts
     *   This function will list all domains on the server that have SSL certificates installed.
     * @return array
     */
    public function getSSLCertificates() {
        $xml = $this->execute('/xml-api/listcrts');
        $arrDomains = array();
        foreach ($xml->crt as $zone) {
            $arrDomains[] = (string) $zone->domain;
        }
        return $arrDomains;
    }

    /**
     * Generate SSLCertificate — generatessl
     *
     * @return array
     */
    public function generateSSLCertificate($xemail, $host, $country, $state, $city,
    $organisation, $department, $email, $password) {
        $xml = $this->execute('/xml-api/generatessl',
        array('xemail'=>$xemail, 'host'=>$host, 'country'=>$country, 'state' => $state,
        'city' => $city, 'co' => $organisation, 'cod' => $department,
        'email'=>$email, 'pass'=>$password));
        $arrCertificate = array();
        $arrCertificate['key'] = (string) $xml->results->key;
        $arrCertificate['keyfile'] = (string) $xml->results->key;
        $arrCertificate['cert'] = (string) $xml->results->cert;
        $arrCertificate['certfile'] = (string) $xml->results->certfile;
        $arrCertificate['csr'] = (string) $xml->results->csr;
        $arrCertificate['pass'] = (string) $xml->results->arg->pass;

        return $arrCertificate;
    }

    public function installWildcardSSLCertificate($domain, $certificate, $key, $certificateAuthorityBundle) {
        $xml = $this->execute('/xml-api/installssl',array('user'=>'nobody', 'domain'=>$domain,
            'cert'=>$certificate, 'key'=>$key, 'cab'=>$certificateAuthorityBundle));
        return true;
    }

    /**
     * Install SSL Certificate — installssl
     *
     * @return boolean
     */
    public function installSSLCertificate($user, $domain, $certificate, $key,
    $ip, $certificateAuthorityBundle = false) {
        $params = array('user'=>$user, 'domain'=>$domain, 'cert'=>$certificate,
        'key'=>$key, 'ip'=>$ip);
        if ($certificateAuthorityBundle !== false) {
            $params['cab']=$certificateAuthorityBundle;
        }
        $xml = $this->execute('/xml-api/installssl',$params);
        return true;
    }

    /**
     * Change a Site's (or User's) IP Address — setsiteip
     * This function allows you to change the IP address of a website,
     * or a user account, hosted on your server.
     *
     * @return boolean
     */
    public function setSiteIp($ip, $user=false, $domain=false) {
        $params = array('ip'=>$ip);
        if ($user === false && $domain === false) {
            //invalid input, minimum one parameter is mandatory
            return false;
        }
        if ($user !== false) {
            $params['user'] = $user;
        }
        if ($domain !== false) {
            $params['domain'] = $domain;
        }

        $xml = $this->execute('/xml-api/setsiteip', $params);
        return true;
    }

    /**
     * Restart Service — restartservice
     *
     * @return boolean
     */
    public function restartService($service) {
        $xml = $this->execute('/xml-api/restartservice', array('service'=>$service));
        return true;
    }

    public function restartDnsService() {
        return $this->restartService('named');
    }

    public function restartHttpdService() {
        return $this->restartService('httpd');
    }

    public function restartMysqlService() {
        return $this->restartService('mysql');
    }

    /**
     * List IP Addresses — listips
     * This function lists all IP addresses bound to network interfaces on the server
     * @return array
     */
    public function getIps() {
        $xml = $this->execute('/xml-api/listips');
        $arrIps = array();
        foreach ($xml->result as $ip) {
            $arrIps[(string) $ip->ip]['ip'] = (string) $ip->ip;
            $arrIps[(string) $ip->ip]['active'] = (string) $ip->active;
            $arrIps[(string) $ip->ip]['dedicated'] = (string) $ip->dedicated;
            $arrIps[(string) $ip->ip]['if'] = (string) $ip->if;
            $arrIps[(string) $ip->ip]['mainaddr'] = (string) $ip->mainaddr;
            $arrIps[(string) $ip->ip]['netmask'] = (string) $ip->netmask;
            $arrIps[(string) $ip->ip]['network'] = (string) $ip->network;
            $arrIps[(string) $ip->ip]['removable'] = (string) $ip->removable;
            $arrIps[(string) $ip->ip]['used'] = (string) $ip->used;
        }
        return $arrIps;
    }
}

?>
