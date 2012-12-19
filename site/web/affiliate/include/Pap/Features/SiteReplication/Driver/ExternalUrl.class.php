<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: LanguageAndDate.class.php 18081 2008-05-16 12:17:32Z mfric $
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
class Pap_Features_SiteReplication_Driver_ExternalUrl extends Pap_Features_SiteReplication_Driver_Base  {
    /**
     * @var Pap_Features_SiteReplication_Site
     */
    private $site;
    private $fileName;
    /**
     * @var Gpf_Net_Http_Response
     */
    private $contentResponse;
    
    public function __construct($fileName, Pap_Features_SiteReplication_Site $site) {
        $this->site = $site;
        $this->fileName = $fileName;
        if ($this->shouldBeProcessed()) {
            $this->loadContent();
        }
    }

    public function shouldBeProcessed() {
        if ($this->fileName == '') {
            return true;
        }
        return $this->site->shouldBeProcessed($this->fileName);
    }

    public function passthru() {
        $getString = $this->encodeArray($_GET);
        if ($getString != '') {
            $getString = '?' . $getString;
        }

        Gpf_Http::setHeader('Location', $this->getReplicatedSiteRealUrl() . $this->fileName . $getString);
    }


    private function loadContent() {
        $getString = $this->encodeArray($_GET);
        if ($getString != '') {
            $getString = '?' . $getString;
        }

        $request = new Gpf_Net_Http_Request();
        $request->setUrl($this->getReplicatedSiteRealUrl() . $this->fileName . $getString);
        $request->setCookies($_COOKIE);

        if (count($_POST) > 0) {
            $request->setMethod('POST');
            $request->setBody($this->encodeArray($_POST));
        }

        $client = new Gpf_Net_Http_Client();
        $this->contentResponse = $client->execute($request);
    }
    
    public function getReplicatedSiteRealUrl() {
        return strrchr($this->site->getData4(), '/') == '/' ? $this->site->getData4() : $this->site->getData4().'/';
    }
    
    /**
     * @return Gpf_Net_Http_Response
     */
    public function getContent() {
        return $this->contentResponse;
    }
}

?>
