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
class GoogleGears_Main extends Gpf_Plugins_Handler {

    /**
     * @return GoogleGears_Main
     */
    public static function getHandlerInstance() {
        return new GoogleGears_Main();
    }

    /**
     * Load javascript library required for google gears
     *
     * @param Gpf_Contexts_Module $context
     */
    public function initJsResources(Gpf_Contexts_Module $context) {
        //install mode will not connect google gears
        if (Gpf_Paths::getInstance()->isInstallModeActive()) {
            return;
        }

        try {

            $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountConfigDirectoryPath() . GoogleGears_Definition::getManifestFileName());

            if (!$file->isExists()) {
                $def = new GoogleGears_Definition();
                $def->generateManifest();
            }

            $context->addJsResource(Gpf_Paths::getInstance()->getFrameworkPluginsDirectoryUrl() . 'GoogleGears/gears_init.js');

            $manifestUrl = Gpf_Paths::getInstance()->getBaseServerUrl() .
            Gpf_Paths::getInstance()->getAccountDirectoryRelativePath() .
            Gpf_Paths::CONFIG_DIR . GoogleGears_Definition::getManifestFileName();
            $context->addJsScript("try {
            var localServer = google.gears.factory.create('beta.localserver');
            var store = localServer.createManagedStore('GwtPHP');
            store.manifestUrl = '$manifestUrl';
            store.checkForUpdate();
            store.enabled = true;
            } catch (e) {}
        ");
        } catch (Gpf_Exception $e) {
        }
    }
}
?>
