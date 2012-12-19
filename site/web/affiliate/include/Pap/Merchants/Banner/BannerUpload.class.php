<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: BannerUpload.class.php 30206 2010-11-30 12:44:30Z mjancovic $
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
class Pap_Merchants_Banner_BannerUpload extends Gpf_File_ImageUpload {
	const BANNERS_DIR = 'banners/';
	
	public function __construct() {
        parent::__construct();
       
	    $this->setRelativeUploadPath(Gpf_Paths::getInstance()->getAccountDirectoryRelativePath() . self::BANNERS_DIR);
	}
	
	/**
	 * @service banner add
     * @param Gpf_Rpc_Params $params 
     * @return Gpf_Rpc_Form
     */
    public function upload(Gpf_Rpc_Params $params) {
    	return parent::upload($params);
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
