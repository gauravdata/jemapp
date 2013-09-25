<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: SqlFile.class.php 23807 2009-03-16 12:48:56Z mbebjak $
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
class Gpf_Install_SqlFile extends Gpf_Install_DbFile {

    public function __construct($fileName, $version, $application) {
        parent::__construct($fileName, $version, $application);
    }
    
    public function getContent() {
        return $this->removeComments($this->file->getContents());
    }
    
    public function getFileName() {
    	return $this->file->getFileName();
    }
    
    protected function executeFile() {
        $db = $this->createDatabase();

        $sql = "SET storage_engine=MYISAM";
        $db->execute($sql);

        $sql = "ALTER DATABASE " . '`' . $db->getDbname() . '`' . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        $db->execute($sql);
        
        foreach(explode(';', $this->getContent()) as $statement) {
            if(strlen(trim($statement))) {
                try {
                    $db->execute(trim($statement));
                } catch (Gpf_DbEngine_SqlException $e) {
                    throw new Gpf_Exception($this->_('Error during database creation.') . $e->getMessage());
                }
            }
        }
    }
    
    private function removeComments($content) {
        return preg_replace('!^--(.*)$!m', '', $content);
    }
}
