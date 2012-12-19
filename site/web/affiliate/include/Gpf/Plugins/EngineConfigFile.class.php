<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Context.class.php 18001 2008-05-13 16:05:33Z aharsani $
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
class Gpf_Plugins_EngineConfigFile extends Gpf_File_Config {
    const FILE_NAME = 'engineconfig.php';
    const CONFIGURATION = 'config';

    public function __construct() {
        parent::__construct(Gpf_Paths::getInstance()->getRealAccountConfigDirectoryPath(). self::FILE_NAME);
    }
    
    public function createEmpty() {
        $file = new Gpf_Io_File($this->getSettingFileName());
        $file->setFileMode('w');
        $file->setFilePermissions(0777);
        $file->write('');
        $file->close();
    }
    
    /**
     *
     * @return Gpf_Plugins_EngineSettings
     */
    public function loadConfiguration() {
		$serialized = $this->getSetting(self::CONFIGURATION);
        $configuration = @unserialize($serialized);
        if(!($configuration instanceof Gpf_Plugins_EngineSettings)) {
            throw new Gpf_Exception('Unserialization error');    		
        }
        return $configuration;
    }

    public function saveConfiguration(Gpf_Plugins_EngineSettings $configuration) {
    	if (defined('ENABLE_ENGINECONFIG_LOG')) {
    		Gpf_Log::info('Writing configuration: ' . print_r($configuration, true));
    	}
        $this->setSetting(self::CONFIGURATION, serialize($configuration));
    }
}

?>
