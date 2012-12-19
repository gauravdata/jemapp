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
class Pap_Features_ZipBanner_Zip extends Pap_Common_Banner {

    
    private $oldFileId;
     /**
     * This method is executed after row object is loaded from database
     */
    protected function afterLoad() {
        parent::afterLoad();
        $this->oldFileId = $this->getData1();
    }
    
    private function deleteTempDir(Gpf_Io_File $dir){
        $dir->emptyFiles(true);
        $dir->rmdir();
    }
    
    public function delete() {
        $this->load();
        if (($this->getData1()!=null) && ($this->getData1()!='')) {
            try {
                $this->deleteTempDir(new Gpf_Io_File($this->getZipFolderUrl().$this->getData1()));
            } catch (Gpf_Exception $e) {
            }
        }
        parent::delete();
    }

    /**
     * Performs any additional actions that are needed before row is saved
     */
    protected function beforeSaveAction() {
        parent::beforeSaveAction();
        if (($this->getData1() != $this->oldFileId) && ($this->oldFileId!='')) {
            $this->deleteTempDir(new Gpf_Io_File($this->getZipFolderUrl().$this->oldFileId));

        }
    }
    
    
    private function getZipFolderUrl() {
        return Gpf_Paths::getInstance()->getAccountDirectoryPath(). Pap_Features_ZipBanner_Unziper::ZIP_DIR ;
    }
    
    protected function getBannerCode(Pap_Common_User $user, $flags) {
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            return $this->getUrl($user);
        }
        $this->getId();
    }
    
    public function getUrl(Pap_Common_User $user) {
        return '';
    }
}

?>
