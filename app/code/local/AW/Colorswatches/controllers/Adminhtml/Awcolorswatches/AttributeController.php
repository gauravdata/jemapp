<?php

class AW_Colorswatches_Adminhtml_Awcolorswatches_AttributeController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes');
    }
    
    public function ajaxFileUploadAction()
    {
        $result = array(
            'success' => true,
            'url' => null,
        );
        try {
            $uploader = new Varien_File_Uploader('file');
            $uploader->setFilesDispersion(true);
            $uploader->setFilenamesCaseSensitivity(false);
            $uploader->setAllowRenameFiles(true);
            $uploader->save(AW_Colorswatches_Helper_Image::getDirPath());
            $result['url'] = AW_Colorswatches_Helper_Image::resizeImage(
                $uploader->getUploadedFileName(), 100, 100
            );
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = $e->getMessage();
        }

        $this->getResponse()->setBody(
            Zend_Json::encode($result)
        );
    }
}