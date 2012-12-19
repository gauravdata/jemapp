<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TemplateForm.class.php 19944 2008-08-18 14:27:23Z mjancovic $
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
class Gpf_Csv_ServerExportFilesForm extends Gpf_Object {

    /**
     * Download export csv file
     *
     * @service export_file export
     * @param Gpf_Rpc_Params $params
     */
    public function downloadFile(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        try {
            $export = new Gpf_Db_Export();
            $export->setPrimaryKeyValue($form->getFieldValue("exportId"));
            $export->load();
            $fileName = $export->getFileName();
        } catch (Gpf_Exception $e) {
            $fileName = ' ';
        }
        $filePath = Gpf_Paths::getInstance()->getAccountDirectoryPath() .
        Gpf_Csv_ImportExportService::EXPORT_DIRECTORY . $fileName;
        $download = new Gpf_File_Download_FileSystem($filePath);
        $download->setAttachment(true);

        return $download;
    }

    /**
     *
     * @service export_file delete
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function deleteFiles(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("File(s) %s was successfully deleted"));
        $action->setErrorMessage($this->_("Could not delete %s file(s)"));

        foreach ($action->getIds() as $id) {
            try {
                $export = new Gpf_Db_Export();
                $export->setPrimaryKeyValue($id);
                $export->load();
                $fileName = $export->getFileName();
                $export->delete();
            } catch (Exception $e) {
                $action->addError();
                continue;
            }

            $filePath = Gpf_Paths::getInstance()->getAccountDirectoryPath() .
            Gpf_Csv_ImportExportService::EXPORT_DIRECTORY . $fileName;

            $file = new Gpf_Io_File($filePath);
            if ($file->isExists()) {
                if ($file->delete()) {
                    $action->addOk();
                } else {
                    $action->addError();
                }
            } else {
                $action->addOk();
            }
        }
        return $action;
    }

    /**
     * Load fileName for import
     *
     * @service export_file read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function loadFileName(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
         
        try {
            $export = new Gpf_Db_Export();
            $export->setPrimaryKeyValue($data->getId());
            $export->load();
            $fileUrl = $this->getFileUrl($export->getFileName());
            $data->setValue("fileName", $fileUrl);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $data->setValue("fileName", "");
        }
         
        return $data;
    }

    private function getFileUrl($fileName) {
        $filePath = Gpf_Paths::getInstance()->getAccountDirectoryPath() .
        Gpf_Csv_ImportExportService::EXPORT_DIRECTORY . $fileName;
        $file = new Gpf_Io_File($filePath);
        if (!$file->isExists()) {
            throw new Gpf_Exception($this->_('File not exist'));
        }
        return $filePath;
    }
}
?>
