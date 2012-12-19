<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Rene Dohanisko
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: BannerUpload.class.php 18513 2008-06-13 15:19:18Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Features_RebrandPdfBanner_Session extends Gpf_Object{

    const SESSION_VAR = 'rebrand_session';

    private $bannerId,$file,$filesize,$upfile,$vars,$pass;

    public function __construct(Pap_Features_RebrandPdfBanner_Banner $banner){
        $this->bannerId = $banner->getId();
    }

    /**
     * @return Pap_Features_RebrandPdfBanner_Session
     */
    public static function load(Pap_Features_RebrandPdfBanner_Banner $banner) {
        $session = Gpf_Session::getInstance();
        $data =  $session->getVar(self::SESSION_VAR);
        if($data && $data->bannerId == $banner->getId()){
            return $data;
        }
        return null;
    }

    public static function clear(){
        $session = Gpf_Session::getInstance();
        $data =  $session->getVar(self::SESSION_VAR);
        if(!$data) return;
        $uploadFile = new Gpf_Io_File($data->getFile());
        $uploadFile->delete();
        $session->setVar(self::SESSION_VAR, null);
    }

    public function saveUpload(Pap_Features_RebrandPdfBanner_Upload $upload) {
        $this->file = $upload->getFile();
        $this->filesize = $upload->getFileSize();
        $this->upfile = $upload->getOriginalFile();
        $this->save();
    }

    public function saveRecognize(array $variables, $password = null) {
        $this->vars =  implode(';',$variables);
        $this->pass = $password;
        $this->save();
    }

    private function save(){
        Gpf_Session::getInstance()->setVar(self::SESSION_VAR, $this);
    }

    public function getBannerId(){
        return $this->bannerId;
    }

    public function getVariables(){
        return explode(';', $this->vars);
    }

    public function getPassword(){
        return $this->pass;
    }

    public function getFile(){
        return $this->file;
    }

    public function getOriginalFile(){
        return $this->upfile;
    }

    public function getFileSize(){
        return $this->filesize;
    }
}
?>
