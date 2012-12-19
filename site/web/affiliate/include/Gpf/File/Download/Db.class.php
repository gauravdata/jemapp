<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DbDownload.class.php 18512 2008-06-13 15:18:51Z aharsani $
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
class Gpf_File_Download_Db extends Gpf_File_DownloadDriver {
    /**
     * @var Gpf_Db_File
     */
    private $file;
    
    public function __construct(Gpf_Db_File $file) {
        $this->file = $file;
    }
    
    protected function getSize() {
        return $this->file->getSize();
    }
    
    protected function getType() {
        return $this->file->getType();
    }
    
    protected function getFileName() {
        return $this->file->getFileName();
    }
    
    protected function getContent() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('content');
        $selectBuilder->from->add(Gpf_Db_Table_FileContents::getName());
        $selectBuilder->where->add('fileid', '=', $this->file->getFileId());
        $selectBuilder->orderBy->add('contentid', true);

        $sth = $this->createDatabase()->execute($selectBuilder->toString());
        while ($row = $sth->fetchArray()) {
            echo $row['content'];
        }
        return '';
    }
}

?>
