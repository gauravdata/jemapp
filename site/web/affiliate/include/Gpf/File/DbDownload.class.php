<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DbDownload.class.php 20140 2008-08-25 15:37:23Z mbebjak $
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
class Gpf_File_DbDownload extends Gpf_Object implements Gpf_Rpc_Serializable {

    /**
     * @var Gpf_Db_File
     */
    private $file;
    private $isAttachment = false;

    /**
     * Download file from server
     *
     * @param Gpf_Rpc_Params $params
     * @service db_file read
     */

    public function download(Gpf_Rpc_Params $params) {
        	
        try {
            $form = new Gpf_Rpc_Form($params);
            if (!strlen($form->getFieldValue('fileid'))) {
                throw new Gpf_Exception("No fileid specified");
            }
            $this->file = new Gpf_Db_File();
            $this->file->set('fileid', $form->getFieldValue("fileid"));
            $this->file->load();

            try {
                if ($form->getFieldValue('attachment') == Gpf::YES) {
                    $this->isAttachment = true;
                } else {
                    $this->isAttachment = false;
                }
            } catch (Exception $e) {
                $this->isAttachment = false;
            }

        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->_("File does not exist"));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $this;
    }

    public function toObject() {
        throw new Gpf_Exception("Unsupported");
    }

    public function toText() {
        if ($this->checkPermissions()) {
            if ($this->downloadContent()) {
                $this->file->incrementDownloads();
            }
        }
        return '';
    }

    private function checkPermissions() {
        // TODO: throw exception in case rights are not sufficient
        // throw new Exception("Permission problem");
        return true;
    }

    private function printHeaders() {
        Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::CONTENT_TYPE, $this->file->get('filetype'));
        if ($this->isAttachment()) {
            Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::CONTENT_DISPOSITION, 'attachment; filename="' . htmlspecialchars($this->file->get('filename')) . '"');
        }
        Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::CONTENT_LENGTH, $this->file->get('filesize'));
        return true;
    }

    /**
     * Print content of file to default output
     */
    private function downloadContent() {
        if ($this->printHeaders()) {
            $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
            $selectBuilder->select->add('content', 'content');
            $selectBuilder->from->add(Gpf_Db_Table_FileContents::getName());
            $selectBuilder->where->add('fileid', '=', $this->file->get('fileid'));
            $selectBuilder->orderBy->add('contentid', true);

            $contents = $selectBuilder->getAllRows();

            foreach ($contents as $contentRecord) {
                echo $contentRecord->get('content');
            }
            return true;
        }
        return false;
    }

    private function isAttachment() {
        return $this->isAttachment;
    }
}

?>
