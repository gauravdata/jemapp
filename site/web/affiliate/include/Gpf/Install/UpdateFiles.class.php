<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Versions.class.php 18552 2008-06-17 12:59:40Z aharsani $
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
class Gpf_Install_UpdateFiles extends Gpf_Object implements Iterator {
    private static $EXTENSIONS = array('sql', 'php');
    
    private $current = false;
    /**
     * @var @array
     */
    private $versions;

    private $application;

    public function __construct($application = Gpf::CODE) {
        $this->application = $application;
    }
    
    /**
     * @return Gpf_Install_DbFile
     */
    public function current() {
        return $this->current;
    }

    public function key() {
        return $this->current->getVersion();
    }

    public function next() {
        $version = array_shift($this->versions);
        if($version === null) {
            $this->current = false;
            return;
        }
        $this->current = $this->createUpdater($version[0], $version[1], $version[2]);
    }

    public function rewind() {
        $this->versions = array();

        foreach (new Gpf_Io_DirectoryIterator(Gpf_Install_DbFile::getDbDirPath($this->application)) 
            as $fullPath => $file) {
            if(preg_match('!^update_(.+)\.(.+)$!', $file, $matches)) {
                $version = $matches[1];
                $extension = $matches[2];
                if(version_compare($version, $this->getInstalledVersion()) > 0
                && in_array($extension, self::$EXTENSIONS)) {
                    $this->versions[$version] = array($file, $version, $extension);
                }
            }
        }
        uksort($this->versions, 'version_compare');
        $this->next();
    }
    
    public function valid() {
        return $this->current !== false;
    }

    private function getInstalledVersion() {
        try {
            return Gpf_Db_Table_Versions::getInstance()->getLatestVersion($this->application);
        } catch (Exception $e) {
        }
        return '0.0.0';
    }
    
    /**
     *
     * @return Gpf_Install_DbFile
     */
    private function createUpdater($file, $version, $type) {
        switch($type) {
            case 'php':
                $updater = new Gpf_Install_PhpDbFile($file, $version,
                $this->application);
                break;
            case 'sql':
            default:
                $updater = new Gpf_Install_SqlFile($file, $version,
                $this->application);
                break;
        }
        return $updater;
    }
}
