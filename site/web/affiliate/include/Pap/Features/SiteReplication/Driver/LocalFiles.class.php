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
class Pap_Features_SiteReplication_Driver_LocalFiles extends Pap_Features_SiteReplication_Driver_Base  {
    /**
     * @var Pap_Features_SiteReplication_Site
     */
    private $site;

    /**
     * @var Gpf_Io_File
     */
    private $siteDir;

    /**
     * @var Gpf_Io_File
     */
    private $file;

    private $relativeFileName;

    public function __construct($fileName, Pap_Features_SiteReplication_Site $site) {
        $this->site = $site;
        $this->siteDir = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountDirectoryPath() . Pap_Features_SiteReplication_Replicator::SITES_DIR . $this->site->getId().'/');
        $this->file = $this->loadFile($fileName);
        $this->relativeFileName = substr($this->file->getFileName(), strlen($this->siteDir->getFileName()));
    }

    public function shouldBeProcessed() {
        return $this->site->shouldBeProcessed($this->relativeFileName);
    }

    public function passthru() {
        Gpf_Http::setHeader("Content-Type", $this->file->getMimeType());
        $this->file->passthru();
    }

    /**
     * @return Gpf_Net_Http_Response
     */
    public function getContent() {
        $getString = $this->encodeArray($_GET);
        if ($getString != '') {
            $getString = '?' . $getString;
        }

        $request = new Gpf_Net_Http_Request();
        $request->setUrl($this->getReplicatedSiteRealUrl().$this->relativeFileName.$getString);
        $request->setCookies($_COOKIE);

        if (count($_POST) > 0) {
            $request->setMethod('POST');
            $request->setBody($this->encodeArray($_POST));
        }

        $client = new Gpf_Net_Http_Client();
        return $client->execute($request);
    }

    public function getReplicatedSiteRealUrl() {
        return Gpf_Paths::getInstance()->getFullAccountServerUrl() . Pap_Features_SiteReplication_Replicator::SITES_DIR . $this->site->getId() . '/';
    }

    /**
     * @return Gpf_Io_File
     */
    private function loadFile($fileName) {
        $file = new Gpf_Io_File($this->siteDir->getFileName().$fileName);
        if ($file->isDirectory()) {
            $file = $this->findIndexFiles($file, array('index.php', 'index.html', 'index.htm'));
        }
        if (!$file->isExists()) {
            throw new Gpf_Exception(sprintf('404 File not found \'%s\'', $fileName));
        }
        return $file;
    }

    private function findIndexFiles(Gpf_Io_File $directory, $files) {
        foreach ($files as $fileName) {
            $file = new Gpf_Io_File($directory->getFileName().$fileName);
            if ($file->isExists()) {
                return $file;
            }
        }
        throw new Gpf_Exception('not found');
    }
}

?>
