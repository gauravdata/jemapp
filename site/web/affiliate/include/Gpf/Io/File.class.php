<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 * 	 @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: File.class.php 34119 2011-08-05 07:53:28Z mkendera $
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
class Gpf_Io_File extends Gpf_Object implements Gpf_Common_Stream {
    const BUFFER_SIZE = 4000;

    private $textFilesExtensions = array('html','php','tpl','stpl','css','sql','txt','TXT','js');
    private $textFileSpecialNames = array('.htaccess','htaccess');

    private $fileName;
    private $extension;
    private $fileMode = 'r';
    private $fileHandler;
    private $isOpened;
    private $filePermissions;

    public function __construct($fileName) {
        $this->fileName = $fileName;
        $this->fileHandler = false;
        $this->isOpened = false;
        $this->filePermissions = null;
    }

    public function __destruct() {
        $this->close();
    }

    public function setFileName($name) {
        $this->fileName = $name;
    }

    public function seek($offset) {
        if(-1 == fseek($this->getFileHandler(), $offset)) {
            throw new Gpf_Exception($this->_('Could not seek file', $this->fileName));
        }
    }

    public function tell() {
        return ftell($this->getFileHandler());
    }

    public function getFileName() {
        return $this->fileName;
    }

    /**
     * Set file mode for operations with file
     *
     * @param string $mode possible values are: 'r','r+','w','w+','a','a+','x','x+'
     */
    public function setFileMode($mode) {
        $this->fileMode = $mode;
    }

    /**
     * Set file permissions in octal mode.
     *
     * @param int
     */
    public function setFilePermissions($permissions) {
        $this->filePermissions = $permissions;
    }

    public function getFileHandler() {
        if($this->fileHandler === false) {
            return $this->open($this->fileMode);
        }
        return $this->fileHandler;
    }

    public function open($fileMode = 'r') {
        $this->fileMode = $fileMode;
        $this->fileHandler = null;
        $this->isOpened = false;
        if(!empty($this->fileName)) {
            if(false !== ($this->fileHandler = @fopen($this->fileName, $this->fileMode))) {
                $this->isOpened = true;
                return $this->fileHandler;
            }
        }
        //TODO: create Gpf_Io_FileException
        throw new Gpf_Exception($this->_('Could not open file') . ' ' . $this->fileName);
    }

    public function lockWrite() {
        return $this->lock(LOCK_EX);
    }

    public function lock($operation) {
        if (!$this->isOpened()) {
            throw new Gpf_Exception('Only opened file can be locked');
        }
        for ($i=1; $i<=10; $i++) {
            if (flock($this->fileHandler, $operation)) {
                return true;
            }
            usleep($i);
        }
        return false;
    }

    private function matchPattern($mask){
        $pattern = '/^'.str_replace('/', '\/', str_replace('\*', '.*', preg_quote(trim($mask)))).'/';
        if (@preg_match($pattern, $this->fileName) > 0) {
            return true;
        }
        return false;
    }

    public function matchPatterns($filePatterns){
        if (is_array($filePatterns)) {
            foreach($filePatterns as $filePattern){
                if ($this->matchPattern($filePattern)){
                    return true;
                }
            }
            return false;
        }
        return $this->matchPattern($filePatterns);
    }

    public function close() {
        if($this->isOpened) {
            @fclose($this->fileHandler);
            $this->fileHandler = false;
            $this->isOpened = false;
        }
    }

    public function readLine($length = 0) {
        $fileHandler = $this->getFileHandler();
        if($length <= 0) {
            return fgets($fileHandler);
        }
        return fgets($fileHandler, $length);
    }

    public function isEof() {
        $fileHandler = $this->getFileHandler();
        return feof($fileHandler);
    }

    public function readAsArray() {
        $result = @file($this->fileName);
        if($result === false) {
            throw new Gpf_Exception($this->_('Could not read file') . ' ' . $this->fileName);
        }
        return $result;
    }

    public function writeLine($string) {
        $fileHandler = $this->getFileHandler();
        $this->changeFilePermissions();
        return fputs($fileHandler, $string);
    }

    public function getSize() {
        return filesize($this->fileName);
    }

    /**
     * Get file extension (computes from filename)
     *
     */
    public function getExtension() {
        if (isset($this->extension)) {
            return $this->extension;
        }
        $info = pathinfo($this->getFileName());
        if(isset($info['extension'])) {
            $this->extension = $info['extension'];
        }
        return $this->extension;
    }

    public function rewind() {
        $fileHandler = $this->getFileHandler();
        if (!@fseek($fileHandler, 0)) {
            throw new Gpf_Exception($this->_('Rewind unsupported in this file stream'));
        }
    }

    public function read($length = 0) {
        $fileHandler = $this->getFileHandler();
        if(true === feof($fileHandler)) {
            return false;
        }
        if($length == 0) {
            $length = $this->getSize();
        }
        return fread($fileHandler, $length);
    }

    public function write($string) {
        if(!($fileHandler = $this->getFileHandler())) {
            throw new Gpf_Exception('Could not write file' . ' ' . $this->fileName);
        }
        $this->changeFilePermissions();
        $result = @fwrite($fileHandler, $string);
        if($result === false || ($result == 0 && strlen($string) != 0)) {
            throw new Gpf_Exception('Could not write file' . ' ' . $this->fileName);
        }
        return $result;
    }

    public function writeCsv($array, $delimiter) {
        if($fileHandler = $this->getFileHandler()) {
            $this->changeFilePermissions();
            $result = @fputcsv($fileHandler, $array, $delimiter);
            if($result === false) {
                throw new Gpf_Exception('Could not write file' . ' ' . $this->fileName);
            }
        }
    }

    public function readCsv($delimiter) {
        $fileHandler = $this->getFileHandler();
        if(true === feof($fileHandler)) {
            return false;
        }
        return fgetcsv($fileHandler, 0, $delimiter);
    }

    public function passthru() {
        $fileHandler = $this->getFileHandler();
        return fpassthru($fileHandler);
    }

    public function getContents() {
        if(!$this->isExists()) {
            throw new Gpf_Exception($this->_('File %s does not exist.', $this->fileName));
        }
        if (($content = @file_get_contents($this->fileName)) === false) {
            throw new Gpf_Exception($this->_('Failed to read file %s', $this->fileName));
        }
        return $content;
    }

    public function putContents($data) {
        if(!$this->isExists()) {
            throw new Gpf_Exception($this->_('File %s does not exist.', $this->fileName));
        }
        if ($content = file_put_contents($this->fileName, $data) === false) {
            throw new Gpf_Exception($this->_('Failed to write file %s', $this->fileName));
        }
        return true;
    }

    public function getCheckSum() {
        if (in_array($this->getFileName(), $this->textFileSpecialNames) || in_array($this->getExtension(), $this->textFilesExtensions)) {
            return md5(str_replace(array("\r\n", "\r"), "\n", $this->getContents()));
        }
        return md5($this->getContents());
    }

    /**
     * Checks if selected file exists
     *
     * @return boolean true if file exists, otherwise false
     */
    public function isExists() {
        return self::isFileExists($this->fileName);
    }

    public static function isFileExists($fileName) {
        return @file_exists($fileName);
    }

    public function isDirectory() {
        return @is_dir($this->fileName);
    }

    public function isWritable() {
        return is_writable($this->fileName);
    }

    public function emptyFiles($recursive = false, $excludeFiles = null) {
        if ($this->isDirectory()) {
            if ($recursive == true) {
                $dir = new Gpf_Io_DirectoryIterator($this, '', false, true);
                foreach ($dir as $fullFileName => $fileName) {
                    $file = new Gpf_Io_file($fullFileName);
                    $file->emptyFiles(true);
                    $file->rmdir();
                }
            }
            $dir = new Gpf_Io_DirectoryIterator($this, '', false);
            foreach ($dir as $fullFileName => $fileName) {
                $file = new Gpf_Io_file($fullFileName);
                 
                if (!is_array($excludeFiles)) {
                    $file->delete();
                }else{
                    if (!in_array($fileName,$excludeFiles)) {
                        $file->delete();
                    }
                }
            }
        } else {
            throw new Gpf_Exception($this->_('%s is not directory!', $this->fileName));
        }
        return true;
    }

    public function rmdir() {
        if (!@rmdir($this->getFileName())) {
            throw new Gpf_Exception($this->_('Could not delete directory %s', $this->fileName));
        }
    }

    /**
     * @throws Gpf_Exception
     */
    public function mkdir($recursive = false, $mode = 0777) {
        $mkMode = $mode;
        if($mkMode === null) {
            $mkMode = 0777;
        }
        if(false === @mkdir($this->fileName, $mkMode, $recursive)) {
            throw new Gpf_Exception($this->_('Could not create directory %s', $this->fileName));
        }
        if($mode !== null) {
            @chmod($this->getFileName(), $mode);
        }
    }

    public function recursiveCopy(Gpf_Io_File $target, $mode = null){
        if ($this->isDirectory()) {
            $dir = new Gpf_Io_DirectoryIterator($this, '', false, true);
            foreach ($dir as $fullFileName => $fileName) {
                $file = new Gpf_Io_File($fullFileName);
                $targetDir = new Gpf_Io_File($target->getFileName() . '/' . $fileName);
                $targetDir->mkdir();
                $file->recursiveCopy($targetDir);
            }
            $dir = new Gpf_Io_DirectoryIterator($this, '', false);
            foreach ($dir as $fullFileName => $fileName) {
                $srcFile = new Gpf_Io_File($fullFileName);
                $dstFile = new Gpf_Io_File($target->getFileName() . '/' . $fileName);

                $this->copy($srcFile, $dstFile);
            }
        } else {
            throw new Gpf_Exception($this->_('%s is not directory!', $this->fileName));
        }
        return true;
    }

    /**
     * @return Gpf_Io_File
     */
    public function getParent(){
        $slashIndex = strrpos($this, '/');
        if($slashIndex == strlen($this) - 1){
            $slashIndex = strrpos($this, '/', -2);
        }
        return new Gpf_Io_File(substr($this, 0, $slashIndex + 1));
    }

    /**
     * @throws Gpf_Exception
     */
    public static function copy(Gpf_Io_File $source, Gpf_Io_File $target, $mode = null) {
        if (Gpf_Php::isFunctionEnabled('copy')) {
            if(false === @copy($source->getFileName(), $target->getFileName())) {
                throw new Gpf_Exception('Could not copy ' .
                $source->getFileName() . ' to ' . $target->getFileName());
            }
        } else {
            $target->open('w');
            $target->write($source->getContents());
        }
        if($mode !== null) {
            @chmod($target->getFileName(), $mode);
        }
    }

    public function getData() {
        return $this->read(self::BUFFER_SIZE);
    }

    public function getInodeChangeTime() {
        clearstatcache();
        return filemtime($this->fileName);
    }
    /**
     * @return boolean
     */
    public function delete() {
        return @unlink($this->getFileName());
    }

    public function getFilePermissions() {
        if (function_exists('fileperms')) {
            return substr(sprintf('%o', @fileperms($this->fileName)), -4);
        }
        return 'not supported';
    }

    public function getFileOwner() {
        if (function_exists('fileowner')) {
            return @fileowner($this->fileName);
        }
        return 'not supported';
    }

    private function changeFilePermissions() {
        if ($this->filePermissions != null) {
            if (!@chmod($this->fileName, $this->filePermissions)) {
                throw new Gpf_Exception($this->_("Could not change permissions %s", $this->fileName));
            }
            $this->filePermissions = null;
        }
    }

    /**
     * Return open status of file
     *
     * @return boolean Returns true if file is opened
     */
    public function isOpened() {
        return $this->isOpened;
    }

    /**
     * Outputs file to the output buffer
     */
    public function output() {
        if (@readfile($this->fileName) == null) {
            if (!Gpf_Php::isFunctionEnabled('fpassthru')) {
                echo file_get_contents($this->fileName);
            } else {
                $fp = fopen($this->fileName, 'r');
                fpassthru($fp);
                fclose($fp);
            }
        }
    }

    public function __toString(){
        return $this->getFileName();
    }

    public function getName() {
        return basename($this->fileName);
    }

    public function getMimeType() {
        return Gpf_Io_MimeTypes::getMimeType($this->getExtension());
    }
}

?>
