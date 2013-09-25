<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ImportFromSource.class.php 19984 2008-08-19 15:08:09Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * Go through all application sources and load source messages
 *
 * @package GwtPhpFramework
 */
class Gpf_Lang_Parser_Sources extends Gpf_Object {
    private $sourceTranslations = array();
    
    /**
     * @var Gpf_Lang_CsvHandler
     */
    private $handler;
    
    public function __construct(Gpf_Lang_CsvHandler $handler) {
        $this->handler = $handler;
    }
    
    /**
     * Defines array of extensions and each extension has array of match patterns
     * Each match pattern should contain ()
     *
     * @var array
     */
    private $matchPatterns = array(
        'php'  => array('/\-\>_\((".*?([\\\][\\\])*[^\\\]")/ms' => Gpf_Lang_Parser_Translation::TYPE_SERVER, 
                        '/\-\>_\((\'.*?([\\\][\\\])*[^\\\]\')/ms' => Gpf_Lang_Parser_Translation::TYPE_SERVER,
                        '/\-\>_sys\((".*?([\\\][\\\])*[^\\\]")/ms' => Gpf_Lang_Parser_Translation::TYPE_SERVER, 
                        '/\-\>_sys\((\'.*?([\\\][\\\])*[^\\\]\')/ms' => Gpf_Lang_Parser_Translation::TYPE_SERVER,
    					'/Gpf_Lang::_\((".*?([\\\][\\\])*[^\\\]")/ms' => Gpf_Lang_Parser_Translation::TYPE_SERVER,
    					'/Gpf_Lang::_\((\'.*?([\\\][\\\])*[^\\\]\')/ms' => Gpf_Lang_Parser_Translation::TYPE_SERVER, 
                        '/Gpf_Lang::_runtime\((".*?([\\\][\\\])*[^\\\]")/ms' => Gpf_Lang_Parser_Translation::TYPE_BOTH, 
                        '/Gpf_Lang::_runtime\((\'.*?([\\\][\\\])*[^\\\]\')/ms' => Gpf_Lang_Parser_Translation::TYPE_BOTH),
        'tpl' => array('/##(.*?)##/ms' => Gpf_Lang_Parser_Translation::TYPE_SERVER),
        'stpl' => array('/##(.*?)##/ms' => Gpf_Lang_Parser_Translation::TYPE_SERVER),
        'java' => array('/\.localize\(\s*(".*?([\\\][\\\])*[^\\\]")/ms' => Gpf_Lang_Parser_Translation::TYPE_CLIENT, 
                        '/\.localizeString\(\s*(".*?([\\\][\\\])*[^\\\]")/ms' => Gpf_Lang_Parser_Translation::TYPE_CLIENT));

    /**
     * Start main import procedure
     */
    public function import() {
        $dirs = array();
        $dirs = array_merge($dirs, Gpf_Paths::getInstance()->getIncludePaths());
        $dirs = array_merge($dirs, Gpf_Paths::getInstance()->getThemesTemplatePaths());
        $dirs[] = Gpf_Paths::getInstance()->getFrameworkTemplatesPath();
        $dirs = array_merge($dirs, Gpf_Paths::getInstance()->getClientPaths());
        $this->importDirectories($dirs);
    }

    /**
     * Iterate through all directories and import translations
     *
     * @param array $dirs
     */
    private function importDirectories($dirs) {
        foreach ($dirs as $dir) {
            $this->importDirectory($dir);
        }
    }

    /**
     * Import translations from given directory
     *
     * @param string $dir Directory path
     */
    private function importDirectory($dir) {
        $sourceIterator = new Gpf_Io_DirectoryIterator($dir, '', true);

        $sourceIterator->addIgnoreDirectory('.svn');

        foreach ($sourceIterator as $fullFilename => $file) {
            $objFile = new Gpf_Io_File($fullFilename);
            if ($this->isExtensionForTranslation($objFile->getExtension())) {
                $this->parseContent($objFile);
            }
        }
    }

    /**
     * Check if extension is allowed in parser and if file should be parsed
     *
     * @param string $extension
     * @return boolean
     */
    private function isExtensionForTranslation($extension) {
        return array_key_exists($extension, $this->matchPatterns);
    }


    /**
     * Parse file content
     *
     * @param unknown_type $content
     * @param Gpf_Io_File $file
     * @return unknown
     */
    private function parseContent(Gpf_Io_File $file) {
        flush();
        try {
            $content = $file->getContents();
        } catch (Gpf_Exception $e) {
            echo $this->_("Failed loading file %s with error: %s", $file->getFileName(), $e->getMessage()) . "<br/>\n";
            return;
        }
        foreach ($this->matchPatterns[$file->getExtension()] as $pattern => $type) {
            $count = preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

            if ($count > 0) {
                foreach ($matches[1] as $sentence) {
                    if (strlen($sentence[0])) {
                        $this->addMessage($sentence[0], $file, $type);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Add parsed message to list of found messages
     *
     * @param string $sourceMessage
     * @param Gpf_Io_File $file
     * @param $type Type of message (server/client/both)
     */
    private function addMessage($sourceMessage, Gpf_Io_File $file, $type) {
        if (in_array($file->getExtension(), array('java', 'php'))) {
            @eval("\$sourceMessage = $sourceMessage;");
        }
        if (!strlen($sourceMessage)) {
            throw new Gpf_Exception($this->_('Source message can\'t be empty in file %s', $file->getFileName()));
        }
        
        $translation = new Gpf_Lang_Parser_Translation();
        $translation->setSourceMessage($sourceMessage);

        if (array_key_exists($translation->getId(), $this->sourceTranslations)) {
            $translation = $this->sourceTranslations[$translation->getId()];
        }
        $translation->setCustomerSpecific(false);
        $translation->setType($type);
        $translation->addModule($this->handler->getModule($file));
        $this->sourceTranslations[$translation->getId()] = $translation;
    }

    /**
     * Get all found translations from sources
     *
     * @return array
     */
    public function getSourceTranslations() {
        return $this->sourceTranslations;
    }
}

?>
