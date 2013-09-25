<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: BannerUpload.class.php 18513 2008-06-13 15:19:18Z aharsani $
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
class Gpf_File_IconUpload extends Gpf_File_UploadBase {

    public function __construct() {
        parent::__construct();
        $this->setAllowedFileExtensions(array('ico'));
        $this->setRelativeUploadPath(Gpf_Paths::getInstance()->getAccountDirectoryRelativePath() . 
                                     Gpf_Paths::FILES_DIRECTORY);
    }
}

?>
