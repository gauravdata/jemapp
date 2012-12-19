<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Gpf_Install_CopyAndBackupDirectoryTask extends Gpf_Install_CopyDirectoryTask {
    protected $resourceOverwritten;
    
    public function __construct(Gpf_Io_File $source, Gpf_Io_File $target) {
        parent::__construct($source, $target, 0777);
    }

    protected function isFileChanged(Gpf_Io_File $source, Gpf_Io_File $target) {
        return $target->getContents() != $source->getContents(); 
    }
    
    protected function copy(Gpf_Io_File $source, Gpf_Io_File $target) {
        $this->resourceOverwritten = false;
        if($target->isExists() && $this->isFileChanged($source, $target)) {
            try {
                Gpf_Io_File::copy($target, new Gpf_Io_File($target->getFileName() . '.v'
                . str_replace('.', '_', Gpf_Application::getInstance()->getVersion())), $this->mode);
                $this->resourceOverwritten = true;
            } catch (Gpf_Exception $e) {
                $message = $this->_('Could not backup changed theme resource file %s (%s)', $target->getFileName(), $e->getMessage());    
                Gpf_Log::error($message);
                throw new Gpf_Exception($message);
            }
        }
        try {
            Gpf_Io_File::copy($source, $target, $this->mode);
        } catch (Gpf_Exception $e) {
            $message = $this->_('Could not install new theme resource (%s) file.  Make sure that file is writable by webserver.', $target->getFileName());
            Gpf_Log::error($message);
            throw new Gpf_Exception($message);
        }
    }
}
?>
