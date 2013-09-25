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
class Gpf_Install_InstallTemplatesTask extends Gpf_Install_CopyDirectoryTask {
    private $sourceOffset;
    
    public function __construct(Gpf_Io_File $source, Gpf_Io_File $target) {
        parent::__construct($source, $target, 0777);
        $this->sourceOffset = strlen(rtrim($source->getFileName(), '/\\')) + 1;    
    }

    protected function copy(Gpf_Io_File $source, Gpf_Io_File $target) {
        parent::copy($source, $target);
        
        $installedTemplate = new Gpf_Db_InstalledTemplate();
        $installedTemplate->setName(substr($source->getFileName(), $this->sourceOffset));
        $installedTemplate->setContentHash(md5($source->getContents()));
        $installedTemplate->insert();
    }
}
?>
