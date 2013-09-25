<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Features_SiteReplication_Replicator extends Gpf_Object {

    const SITES_DIR = 'sites/';
    const TEST_STRING = '___TEST_REPLICATOR___';
    const TEST_RESPONSE = '___REPLICATOR_OK___';

    /**
     * @var Pap_Affiliates_User
     */
    private $user;
    /**
     * @var Pap_Features_SiteReplication_Site
     */
    private $site;

    private $content;

    /**
     * @var Pap_Features_SiteReplication_Driver_Base
     */
    private $driver;
    
    /**
     * 
     * @var Pap_Tracking_Request
     */
    private $request;

    public function __construct() {
        $this->request = new Pap_Tracking_Request();
        $this->site = $this->loadSite($this->request->getRequestParameter('a_bid'));
        $userId = $this->request->getRequestParameter('a_aid');
        $fileName = $this->request->getRequestParameter('a_file');

        $this->checkIfTestCall($userId, $fileName);
        $this->user = $this->loadUser($userId);

        if ($this->request->getRequestParameter('a_redir') == Gpf::YES) {
            Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::LOCATION, $this->site->getUrl($this->user));
            exit();
        }

        if ($this->site->getSourceType() == Pap_Features_SiteReplication_Site::SOURCE_EXTERNAL_URL) {
            $this->driver = new Pap_Features_SiteReplication_Driver_ExternalUrl($this->sanitizeFileName($fileName), $this->site);
        } else {
            $this->driver = new Pap_Features_SiteReplication_Driver_LocalFiles($this->sanitizeFileName($fileName), $this->site);
        }
    }

    public function shouldBeProcessed() {
        return $this->driver->shouldBeProcessed();
    }

    private function checkIfTestCall($userId, $fileName) {
        if ($userId == self::TEST_STRING || $fileName == self::TEST_STRING) {
            die(self::TEST_RESPONSE);
        }
    }

    private function sanitizeFileName($fileName) {
        $fileName = str_replace('../', '', $fileName);
        $fileName = str_replace('..\\', '', $fileName);

        $fileName = str_replace('.\\', '', $fileName);
        $fileName = str_replace('./', '', $fileName);

        return $fileName;
    }

    private function loadUser($id) {
        try {
            return Pap_Affiliates_User::loadFromId($id);
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception($this->_sys('Error loading user \'%s\'', $id));
        }
    }

    /**
     * @return Pap_Features_SiteReplication_Site
     */
    private function loadSite($siteId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->from->add(Pap_Db_Table_Banners::getName());
        $select->select->addAll(Pap_Db_Table_Banners::getInstance());
        $whereCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $whereCondition->add(Pap_Db_Table_Banners::ID, '=', $siteId, 'OR');
        $whereCondition->add(Pap_Db_Table_Banners::DATA1, '=', 'D'.$siteId, 'OR');
        $select->where->addCondition($whereCondition);
        $select->where->add(Pap_Db_Table_Banners::TYPE, '=', Pap_Features_SiteReplication_Config::BannerTypeSite);

        try {
            $bannerFactory = new Pap_Common_Banner_Factory();
            return $bannerFactory->getBannerFromRecord($select->getOneRow());
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception($this->_sys('Replicated site \'%s\' does not exist', $siteId));
        }
    }

    private function replaceVariables() {
        $userFields = Pap_Common_UserFields::getInstance();
        $userFields->setUser($this->user);
        $this->content = $userFields->replaceUserConstantsInText($this->content);
        $this->content = $this->replaceRelativeLinks($this->content);
        $this->content = $this->replaceAbsoluteLinks($this->content);
    }

    private function replaceRelativeLinks($text) {
        return str_replace('../' . Gpf_Paths::getInstance()->getAccountDirectoryRelativePath(),
            Gpf_Paths::getInstance()->getFullAccountServerUrl(), $text);
    }

    private function addClickTrackingCode() {
        if (($startOfBody = strpos(strtolower($this->content), '<body')) === false) {
            return;
        }
        $endOfBody = strpos($this->content, '>', $startOfBody) + 1;

        $this->content = substr($this->content, 0, $endOfBody).
        $this->getClickTrackingCode().
        substr($this->content, $endOfBody);
    }

    private function getClickTrackingCode() {
        $code  = '<script id="pap_x2s6df8d" src="'.Gpf_Paths::getInstance()->getFullScriptsUrl().'clickjs.php" type="text/javascript">'."\n";
        $code .= '</script>'."\n";
        $code .= '<script type="text/javascript">'."\n";
        $code .= '<!--'."\n";
        $code .= "var AffiliateID='".$this->user->getRefId()."'\n";
        $code .= "var BannerID='".$this->site->getId()."'\n";
        if($this->request->getRequestParameter('channel') != '') {
            $code .= "var Channel='".$this->request->getRequestParameter('channel')."'\n";
        }
        if($this->request->getRequestParameter('data1') != '') {
            $code .= "var Data1='".$this->request->getRequestParameter('data1')."'\n";
        }
        if($this->request->getRequestParameter('data2') != '') {
            $code .= "var Data2='".$this->request->getRequestParameter('data2')."'\n";
        }
        $code .= 'papTrack();'."\n";
        $code .= '//-->'."\n";
        $code .= '</script>'."\n";
        return $code;
    }

    private function getReplicatedSiteRealUrl() {
        return $this->driver->getReplicatedSiteRealUrl();
    }

    private function replaceAbsoluteLinks($text) {
        $text = str_replace($this->getReplicatedSiteRealUrl(), $this->site->getUrl($this->user), $text);
        return $text;
    }

    public function passthru() {
        $this->driver->passthru();
    }

    public function getReplicatedContent() {
        $this->fetchContent();
        $this->replaceVariables();
        $this->addClickTrackingCode();
        header('Content-Length: '.strlen($this->content));
        return $this->content;
    }

    private function encodeSubArray(array $array, $keyName) {
        $string = "";
        foreach ($array as $key => $value) {
            $string .= $keyName . '[' . $key . ']='.urlencode($value).'&';
        }
        return rtrim($string, "&");
    }

    private function encodeArray(array $array) {
        $string = "";
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $string .= $this->encodeSubArray($value, $key) . '&';
            } else {
                $string .= $key. '=' . urlencode($value) . '&';
            }
        }
        return rtrim($string, "&");
    }

    private function fetchContent() {
        $response = $this->driver->getContent();
        $this->content = $response->getBody();

        if($this->site->getData5() == Gpf::YES) {
            $this->content = str_replace('%7B', '{', $this->content);
            $this->content = str_replace('%7D', '}', $this->content);
        }
        
        foreach (explode("\n", $response->getHeadersText()) as $headerLine) {
            if (strstr($headerLine, 'Transfer-Encoding')) {
                continue;
            }
            if (strstr($headerLine, 'Content-Length')) {
                continue;
            }
            header($headerLine, true);
        }
    }
}

?>
