<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Renko Dohanik
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
class Pap_Features_RebrandPdfBanner_Banner extends Pap_Common_Banner {

    const BANNERID = 'bannerId';

    public function getPreview(Pap_Common_User $user) {
        return $this->_('File Name').': <strong>'.$this->getFileName().'</strong><br/>';
    }

    protected function getBannerCode(Pap_Common_User $user, $flags) {
        return $this->getBannerScriptUrl($user);
    }

    /**
     * @return array
     */
    public function getVariables() {
        return explode(':', $this->getData2());
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables) {
        $this->setData2(implode(':', $variables));
    }

    /**
     * @param $variable
     * @return boolean
     */
    public function removeVariable($variable) {
        $variables = $this->getVariables();
        if (($key = array_search($variable, $variables)) !== false) {
            unset($variables[$key]);
            $this->setVariables($variables);
            return true;
        }
        return false;
    }

    /**
     * @param $form
     * @return Pap_Features_RebrandPdfBanner_Banner
     */
    public static function create(Gpf_Rpc_Params $params){
        $form = new Gpf_Rpc_Form($params);
        if($form->existsField(self::BANNERID)){
            $banner = new Pap_Features_RebrandPdfBanner_Banner();
            $banner->setId($form->getFieldValue(self::BANNERID));
            return $banner;
        }
        throw new Gpf_Exception('No banner Id Found');
    }

    public function save() {
        if($sessionData = Pap_Features_RebrandPdfBanner_Session::load($this)){
            $this->cleanSavedFile();
            $this->setData1($this->renameTempFile($sessionData->getFile()));
            $this->setData2(implode(':',$sessionData->getVariables()));
            $this->setData3($sessionData->getOriginalFile());
            $this->setData4($sessionData->getFileSize());
            $this->setData5($sessionData->getPassword());
            Pap_Features_RebrandPdfBanner_Session::clear();
        }
        parent::save();
    }

    public function getFileName(){
        $path = $this->get(Pap_Db_Table_Banners::DATA3);
        $split = explode('/', $path);
        return $split[count($split) - 1];
    }

    public function cleanSavedFile(){
        if($this->isSaved()){
            $this->load();
            $file = new Gpf_Io_File($this->getData1());
            $file->delete();
        }
    }

    public function setId($id){
        if($id == 'null'){
            return;
        }
        parent::setId($id);
    }

    private function renameTempFile($tmpFileName){
        $fileName = substr($tmpFileName, 0, strlen($tmpFileName) - 4);
        if(@rename($tmpFileName, $fileName) === false){
            throw new Gpf_Exception('Error: Pdf file was not renamed from tmp extension');
        }
        return $fileName;
    }

    private function isSaved(){
        return $this->getId() !== null && $this->getId() !== '';
    }
}
?>
