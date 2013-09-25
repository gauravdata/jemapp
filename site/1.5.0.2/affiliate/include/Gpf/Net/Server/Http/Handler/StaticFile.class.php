<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Handler.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Net_Server_Http_Handler_StaticFile extends Gpf_Object {
    private $path;

    /**
     * Extension to Content Type mapping
     *
     * @var array (Extenstion => Content Type)
     */
    private static $extensionContentType = array(
        'html' => 'text/html',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'css' => 'text/css',
        'js' => 'application/x-javascript',
        'gz' => 'application/x-gzip');

    private $serverRootUrl;

    public function __construct($serverRootUrl, $path = '') {
        $this->serverRootUrl = $serverRootUrl;
        $this->path = $path;
    }

    /**
     * Return true if extension is supported by this handler
     *
     * @param string $extension
     * @return boolean
     */
    public static function isSupportedExtension($extension) {
        if (array_key_exists($extension, self::$extensionContentType)) {
            return true;
        }
        return false;
    }

    /**
     * Return content type to given extension
     *
     * @param string $extension
     * @return string
     */
    public static function getContentType($extension) {
        if (!self::isSupportedExtension($extension)) {
            throw new Gpf_Exception("Extension $extension is not supported");
        }
        return self::$extensionContentType[$extension];
    }

    /**
     * Parse extension from file path
     *
     * @param string $filePath
     * @return string
     */
    public static function getFileExtension($filePath) {
        $path = pathinfo($filePath);
        return strtolower($path['extension']);
    }

    private function realpath($rootPath, $path) {
        $path = explode('/', $path);
        $rootPath = explode('/', rtrim($rootPath, '/'));
        foreach ($path as $id => $pathDir) {
            if ($pathDir != '..') {
                break;
            }
            unset($path[$id]);
            array_pop($rootPath);
        }

        return implode('/', $rootPath) . '/' . implode('/', $path);
    }

    private function getFileName($url) {
        $rootUrl = $this->realpath($this->serverRootUrl, $this->path);
        if (($pos = strpos($url, $rootUrl)) !== 0) {
            throw new Gpf_Exception("Url $url not found in $rootUrl");
        }
        $relativePath = substr($url, strlen($rootUrl));
        return Gpf_Paths::getInstance()->getTopPath() . $this->path . $relativePath;
    }

    
    private function computeEtag(Gpf_Io_File $file) {
        if(!$file->isExists()) {
            return '';
        }
        return md5($file->getSize() . '|' . $file->getInodeChangeTime());
    }
    
    /**
     * Check if file can be cached. Files containing word nocache will not be cached
     *
     * @param Gpf_Io_File $file
     * @return boolean
     */
    private function isCacheableFile(Gpf_Io_File $file) {
        if (strpos($file->getFileName(), 'nocache') !== false) {
            return false;
        }
        return true;
    }
    
    /**
     *
     * @param Gpf_Net_Server_Http_Request $request
     * @return Gpf_Net_Server_Http_Response
     */
    public function handle(Gpf_Net_Server_Http_Request $request) {
        $file = new Gpf_Io_File($this->getFileName($request->getPath()));
        $fileETag = $this->computeEtag($file);
        Gpf_Log::debug($request->toString());
        if ($this->isCacheableFile($file) && $request->ifNoneMatch($fileETag)) {
            $response = new Gpf_Net_Server_Http_Response(304, $file);
            $response->setConnection('Keep-Alive');
            $response->setBody("Not Modified");
            $response->setETag($fileETag);
            Gpf_Log::debug($this->_sys("Resource not modified, returned 304 for %s", $request->getPath()));
            return $response;
        }

        //TODO load file from memory cache if available and not from file
        // - but I'm not sure if file cache will help - it can just use quite huge amount of server memory

        try {
            $file->open();
        } catch (Gpf_Exception $e) {
            $response = new Gpf_Net_Server_Http_Response(404);
            $response->setBody('File not found');
            Gpf_Log::info($e->getMessage());
            return $response;
        }

        $response = new Gpf_Net_Server_Http_StreamResponse(200, $file);
        $response->setConnection('Keep-Alive');
        $response->setContentType(self::getContentType(self::getFileExtension($request->getPath())));
        $response->setContentLength($file->getSize());
        if ($this->isCacheableFile($file)) $response->setETag($fileETag);
        Gpf_Log::debug($this->_sys("Return static file %s" . $request->getPath()));
        return $response;
    }

    public function onStart() {

    }

    public function onIdle() {

    }

    public function onShutdown() {

    }

    public function onConnect() {

    }

    public function onDisconnect() {

    }

    public function onConnectionRefused() {

    }

    public function onReceiveData() {

    }
}
?>
