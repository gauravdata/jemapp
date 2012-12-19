<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class GoogleGears_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'GoogleGears';
        $this->name = 'Google Gears';
        $this->description = 'Plugin will cache static data in your browser if in browser is installed Google Gears. With Google Gears should application load much faster (after first load), because most of files will be loaded from cache of browser and not from server';
        $this->version = '1.0.1';
        $this->help = 'Also in case you will activate plugin, Google Gears will be used only in case, that user has installed this browser plugin from here: <a href="http://gears.google.com/" target="_blank">Google Gears</a>.';
        $this->addImplementation('Core.initJsResources', 'GoogleGears_Main', 'initJsResources');
    }

    public static function getManifestFileName() {
        return md5(Gpf_Application::getInstance()->getInstalledVersion()) . '.txt';
    }

    public function generateManifest() {
        $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountConfigDirectoryPath() . self::getManifestFileName());
        $file->setFileMode('w');
        $file->open('w');

        if ($file->isOpened()) {
            $file->write('{"betaManifestVersion":1,"version":"' .
            Gpf_Application::getInstance()->getCode() . '_' .
            Gpf_Application::getInstance()->getInstalledVersion() . md5(rand()) .
            '","entries":[{"url":""}');

            //add js libraries
            $this->addJsLibraries($file);

            //add images, css and other theme resources
            $this->addImages($file);

            $file->write(']}');
            $file->close();
        }
    }

    /**
     * On activate generate gears manifest file
     */
    public function onActivate() {
        $this->generateManifest();
    }

    private function addJsLibraries(Gpf_Io_File $file) {
        //TODO it is not clear yet how to identify application directory, where are compiled js libraries
    }

    /**
     * Add images urls to manifest
     *
     * @param Gpf_Io_File $file
     */
    private function addImages(Gpf_Io_File $file) {
        $dir = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getAccountDirectoryPath() .
        Gpf_Paths::TEMPLATES_DIR, '', true);
        foreach ($dir as $fullName => $fileName) {
            $info = pathinfo($fullName);
            if(isset($info['extension'])) {
                switch($info['extension']) {
                    case 'png':
                    case 'jpg':
                    case 'gif':
                    case 'ico':
                    case 'css':
                        $file->write(',{"url":"../../' . $fullName . '"}' . "\n");
                        break;
                    default:
                }
            }

        }
    }

    /**
     * On deactivate remove manifest file
     */
    public function onDeactivate() {
        try {
            $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountConfigDirectoryPath() . self::getManifestFileName());
            $file->delete();
        } catch (Exception $e) {
        }
    }
}

?>
