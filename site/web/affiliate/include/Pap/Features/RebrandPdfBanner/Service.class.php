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
class Pap_Features_RebrandPdfBanner_Service extends Gpf_Object{

    /**
     * @service banner read
     */
    public function showPreview(Gpf_Rpc_Params $params) {
        $banner = Pap_Features_RebrandPdfBanner_Banner::create($params);
        $banner->load();
        $form = new Gpf_Rpc_Form($params);
        $user = new Pap_Common_User();
        $user->setId($form->getFieldValue('affiliateId'));
        $user->load();
        $processor = new Pap_Features_RebrandPdfBanner_Pdf_Processor();
        $processor->showBannerByUser($user, $banner);
    }

    /**
     * @service banner read
     */
    public function getVariablesRpc(Gpf_Rpc_Params $params) {
        return new Gpf_Rpc_Map(Pap_Features_RebrandPdfBanner_Variables::getAll());
    }

    /**
     * @service banner write
     */
    public function upload(Gpf_Rpc_Params $params) {
        Pap_Features_RebrandPdfBanner_Session::clear();
        $upload = $this->createUpload();
        $form = $upload->upload($params);
        if($form->isSuccessful()){
            $banner = Pap_Features_RebrandPdfBanner_Banner::create($params);
            $session = new Pap_Features_RebrandPdfBanner_Session($banner);
            $session->saveUpload($upload);
        }
        return $form;
    }

    /**
     * @service banner write
     * @return Gpf_Rpc_Form
     */
    public function recognize(Gpf_Rpc_Params $params) {
        @set_time_limit(240);
        $banner = Pap_Features_RebrandPdfBanner_Banner::create($params);
        $form = new Gpf_Rpc_Form($params);
        $session = Pap_Features_RebrandPdfBanner_Session::load($banner);
        try{
            $document = $this->createDocument($session->getFile());
            if($document->isEncrypted()){
                $form->setField('status', 'encrypted');
            }else{
                $this->processVariables($document,$form,$banner,$session);
            }
        }catch (Gpf_Pdf_Exception $e){
            $form->setErrorMessage($this->_('Pdf Parser Error: ') . $e->getMessage());
        }
        return $form;
    }

    private function processVariables(Gpf_Pdf_Document $document, Gpf_Rpc_Form $form,
    Pap_Features_RebrandPdfBanner_Banner $banner, Pap_Features_RebrandPdfBanner_Session $session){
        $processor = $this->createProcessor();
        $variables = $processor->searchVars($document);
        $document->dispose();
        if(count($variables) == 0){
            Pap_Features_RebrandPdfBanner_Session::clear();
            $form->setField('status', 'no_variables');
            $form->setInfoMessage($this->_('Pdf Not Uploaded , No Variables Found !'));
            return;
        }
        $session->saveRecognize($variables);
        $this->saveInfoToForm($form, $banner, $variables);
        $form->setInfoMessage($this->_("File was succesfuly uploaded"));
    }

    private function saveInfoToForm(Gpf_Rpc_Form $form, Pap_Features_RebrandPdfBanner_Banner $banner, array $variables){
        $session  = Pap_Features_RebrandPdfBanner_Session::load($banner);
        $form->setField('file', $session->getOriginalFile());
        $form->setField('filesize', $session->getFileSize());
        $form->setField('vars', implode(':',$variables));
    }

    /**
     * @service banner write
     */
    public function authenticate(Gpf_Rpc_Params $params){
        $banner = Pap_Features_RebrandPdfBanner_Banner::create($params);
        $session = Pap_Features_RebrandPdfBanner_Session::load($banner);
        $form = new Gpf_Rpc_Form($params);
        $document = $this->createDocument($session->getFile());
        if($document->authenticate($password = $form->getFieldValue('password'))){
            $this->processVariables($document,$form,$banner,$session);
        }else{
            $form->setInfoMessage($this->_('Bad Password'));
            $form->setField('status', 'encrypted');
        }
        return $form;
    }

    /**
     *@return Pap_Features_RebrandPdfBanner_Upload
     */
    protected function createUpload(){
        return new Pap_Features_RebrandPdfBanner_Upload();
    }

    /**
     *@return Gpf_Pdf_Document
     */
    protected function createDocument($file){
        return Gpf_Pdf_Document::loadFileName($file);
    }

    /**
     *@return Pap_Features_RebrandPdfBanner_Pdf_Processor
     */
    protected function createProcessor(){
        return new Pap_Features_RebrandPdfBanner_Pdf_Processor();
    }
}
?>
