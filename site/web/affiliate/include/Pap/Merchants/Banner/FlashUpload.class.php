<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FlashUpload.class.php 23000 2009-01-07 15:46:55Z mbebjak $
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
class Pap_Merchants_Banner_FlashUpload extends Gpf_File_UploadBase {

    public function __construct() {
        parent::__construct();
        $this->setAllowedFileExtensions(array('swf'));
        $this->setRelativeUploadPath(Gpf_Paths::getInstance()->getAccountDirectoryRelativePath() . 
                                     Pap_Merchants_Banner_BannerUpload::BANNERS_DIR);
    }
    
    protected function saveUploadedFile() {
        $file = parent::saveUploadedFile();

        $file = str_replace('../' . Gpf_Paths::getInstance()->getAccountDirectoryRelativePath(), 
                            Gpf_Paths::getInstance()->getFullAccountServerUrl(),                        
                            $file);

        return $file;
    }
}

?>
