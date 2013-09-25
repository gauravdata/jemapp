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
class Pap_Features_SiteReplication_Site extends Pap_Common_Banner {

    const SOURCE_LOCAL_FILES = 'L';
    const SOURCE_EXTERNAL_URL = 'E';

    protected function getBannerCode(Pap_Common_User $user, $flags) {
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            return $this->getUrl($user);
        }
        return $this->getDestinationUrl();
    }

    public function getUrl(Pap_Common_User $user) {
        return $this->getDestinationUrl() . $user->getRefId() . '/';
    }

    public function getHtaccessCode(Pap_Common_User $user = null) {
        $code  = '# Start Post Affiliate Site Replication Code'."\n";
        $code .= 'RewriteEngine on'."\n\n";
        if ($user == null) {
            $code .= 'RewriteCond %{REQUEST_FILENAME} !-d'."\n";
            $code .= 'RewriteCond %{REQUEST_FILENAME} !-f'."\n";
            $code .= 'RewriteRule ^([^/]+)/(.*) '.Gpf_Paths::getInstance()->getFullScriptsUrl().'page.php?a_aid=$1&a_bid='.$this->getId().'&a_file=$2 [L,P,QSA]'."\n";
            $code .= "\n";
            $code .= 'RewriteCond %{REQUEST_FILENAME} !-d'."\n";
            $code .= 'RewriteCond %{REQUEST_FILENAME} !-f'."\n";
            $code .= 'RewriteRule ^([^/]+) '.Gpf_Paths::getInstance()->getFullScriptsUrl().'page.php?a_aid=$1&a_bid='.$this->getId().'&a_redir=Y [L,P,QSA]'."\n";
        } else {
            $code .= 'RewriteCond %{REQUEST_FILENAME} !-d'."\n";
            $code .= 'RewriteCond %{REQUEST_FILENAME} !-f'."\n";
            $code .= 'RewriteRule ^(.*) '.Gpf_Paths::getInstance()->getFullScriptsUrl().'page.php?a_aid='.$user->getRefId().'&a_bid='.$this->getId().'&a_file=$1 [P,QSA]'."\n";
        }
        $code .= "\n";
        $code .= '# End of Post Affiliate Site Replication Code'."\n";
        return $code;
    }

    public function shouldBeProcessed($fileName) {
        $filesToProcess = explode(',', $this->getData2());
        foreach ($filesToProcess as $filePattern) {
            $pattern = '/^'.str_replace('/', '\/', str_replace('\*', '.*', preg_quote(trim($filePattern)))).'/';
            if (@preg_match($pattern, $fileName) > 0) {
                return true;
            }
        }
        return false;
    }

    public function getSourceType() {
        if (in_array($this->getData3(), array(self::SOURCE_EXTERNAL_URL, self::SOURCE_LOCAL_FILES))) {
            return $this->getData3();
        }
        return self::SOURCE_LOCAL_FILES;
    }

    public function getSourceUrl() {
        if ($this->getSourceType() == self::SOURCE_LOCAL_FILES) {
            return Gpf_Paths::getInstance()->getFullAccountServerUrl() . Pap_Features_SiteReplication_Replicator::SITES_DIR . $this->getId() . '/';
        }
        return $this->getData4();
    }

    public function insert() {
        parent::insert();
        $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountDirectoryPath() . Pap_Features_SiteReplication_Replicator::SITES_DIR . $this->getId());
        $file->mkdir();
    }
}

?>
