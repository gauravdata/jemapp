<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Gpf_File_FilesTree extends Gpf_Ui_LoadableTree {

    const TYPE_FILE = 'F';
    const TYPE_DIRECTORY = 'D';
    
    /**
     * @service uploaded_file read
     * @param $itemId
     * @return Gpf_Data_RecordSet
     */
    public function loadTree(Gpf_Rpc_Params $params) {
        return parent::loadTree($params);
    }

    protected function loadItems($itemId) {
        $result = new Gpf_Data_RecordSet();
        $result->setHeader(array('itemId', 'subItemsCount', 'name', 'info', 'type'));

        if (Gpf_Io_File::isFileExists($itemId)) {
            $file = new Gpf_Io_File($itemId);
            if ($file->isDirectory()) {
                $this->loadSubfilesFiles($itemId, $result, true);
                $this->loadSubfilesFiles($itemId, $result);
                return $result;
            }
            $result->add(array($itemId, 0, $file->getName(), $file->getSize(), self::TYPE_FILE));
        }
       
        return $result;
    }

    private function loadSubfilesFiles($fileUrl, Gpf_Data_RecordSet $result, $directories = false) {
        foreach (new Gpf_Io_DirectoryIterator($fileUrl, '', false, $directories) as $fullFileName => $file) {
            $file = new Gpf_Io_File($fullFileName);
            if ($file->isDirectory()) {
                $filesData = $this->getFilesData($fullFileName);
                $result->add(array($fullFileName, $filesData['count'], $file->getName(), $filesData['size'], self::TYPE_DIRECTORY));
                continue;
            }
            $result->add(array($fullFileName, 0, $file->getName(), $file->getSize(), self::TYPE_FILE));
        }
    }

    private function getFilesData($fileUrl) {
        $filesData = array();
        $filesData['count'] = 0;
        $filesData['size'] = 0;
        foreach (new Gpf_Io_DirectoryIterator($fileUrl) as $fullFileName => $file) {
            $file = new Gpf_Io_File($fullFileName);
            $filesData['count']++;
            $filesData['size'] += $file->getSize();
        }
        return $filesData;
    }
}
?>
