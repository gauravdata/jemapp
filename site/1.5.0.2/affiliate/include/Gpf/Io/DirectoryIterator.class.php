<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DirectoryIterator.class.php 23797 2009-03-16 07:34:47Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * Gpf_Io_DirectoryIterator
 * 
 * Iterate over directory 
 * Supports recursive iteration, file filtering based on extension and directory
 * filtering
 *  
 * @package GwtPhpFramework
 */
class Gpf_Io_DirectoryIterator extends Gpf_Object implements Iterator {
    private $directory;
    private $recursive;
    private $onlyDirectories;
    private $files;
    private $pos = -1;
    private $extension;
    private $iterator = null;
    private $ignoreDirectories = array();
    private $ignoreAbsoluteDirectories = array();

    public function __construct($directory, $extension = '', $recursive = false, $onlyDirectories = false) {
        $this->directory = $this->normalizeDirectory($directory);
        $this->recursive = $recursive;
        $this->extension = $extension;
        $this->onlyDirectories = $onlyDirectories;
    }

    private function normalizeDirectory($dir) {
        if(strlen($dir) <= 0) {
            return false;
        }
        $dir = str_replace('\\', '/', $dir);
        if(substr($dir, -1) != '/') {
            $dir .= '/';
        }
        return $dir;
    }

    public function current() {
        return $this->iterator->current();
    }

    public function key() {
        return $this->iterator->key();
    }

    public function next() {
        if($this->iterator != null && $this->iterator->valid()) {
            $this->iterator->next();
            if(!$this->iterator->valid()){
                $this->iterator = null;
                $this->next();
            }
        }
        while($this->iterator == null || !$this->iterator->valid()) {
            $this->pos++;
            if(!$this->valid()) {
                return;
            }
            $this->iterator = $this->files[$this->pos];
            $this->iterator->rewind();
        }
    }

    public function rewind() {
        $this->files = array();

        if (!($handle = @opendir($this->directory))) {
            $this->next();
            return;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file == "." || $file == "..") {
                continue;
            }
            $filename = $this->directory . $file;
            if ($this->onlyDirectories && @is_dir($filename . '/')) {
                $this->files[$file] = new Gpf_Io_FileIterator($filename, $file);
                continue; 
            }
            if(@is_dir($filename . '/')) {
                if($this->recursive && !in_array($filename . '/', $this->ignoreAbsoluteDirectories) &&
                !in_array($file, $this->ignoreDirectories)
                ) {
                    $this->files[$file] = $this->create($filename . '/');
                }
            } else if (!$this->onlyDirectories) {
                if($this->hasExtension($file)) {
                    $this->files[$file] = new Gpf_Io_FileIterator($filename, $file);
                }
            }
        }
        ksort($this->files);
        $this->files = array_values($this->files);
        closedir($handle);
        $this->next();
    }

    private function create($directory) {
        $iterator = new Gpf_Io_DirectoryIterator($directory, $this->extension, $this->recursive);
        $iterator->setIgnoreDirectories($this->ignoreDirectories);
        $iterator->setIgnoreAbsoluteDirectories($this->ignoreAbsoluteDirectories);
        return $iterator;
    }

    private function hasExtension($file) {
        if($this->extension != '') {
            if(false === strrpos($file, $this->extension) 
                || strrpos($file, $this->extension) != strlen($file) - strlen($this->extension)) {
                return false;
            }
        }
        return true;
    }

    public function valid() {
        return $this->pos < count($this->files);
    }

    public function addIgnoreDirectory($dir) {
        $this->ignoreDirectories[] = $dir;
    }

    public function addIgnoreAbsoluteDirectory($dir) {
        $this->ignoreAbsoluteDirectories[] = $this->normalizeDirectory($dir);
    }
    
    public function setIgnoreDirectories(array $dirs) {
        $this->ignoreDirectories = $dirs;
    }

    public function setIgnoreAbsoluteDirectories($dirs) {
        $this->ignoreAbsoluteDirectories = $dirs;
    }
}

class Gpf_Io_FileIterator extends Gpf_Object implements Iterator {
    private $file;
    private $fullFileName;
    private $valid = true;
    
    public function __construct($fullFileName, $file) {
        $this->file = $file;
        $this->fullFileName = $fullFileName;
    }

    public function current() {
        return $this->file;
    }

    public function key() {
        return $this->fullFileName;
    }

    public function next() {
        $this->valid = false;
    }

    public function rewind() {
    }

    public function valid() {
        return $this->valid;
    }
}
