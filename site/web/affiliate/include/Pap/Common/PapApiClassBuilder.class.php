<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class Pap_Common_PapApiClassBuilder extends Gpf_Common_ClassMerger {

    protected function getHeader() {
        return  "/**\n".
                " *   @copyright Copyright (c) 2008-2009 Quality Unit s.r.o.\n".
                " *   @author Quality Unit\n".
                " *   @package PapApi\n".
                " *   @since Version 1.0.0\n".
                " *   \n".
                " *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,\n".
                " *   Version 1.0 (the \"License\"); you may not use this file except in compliance\n".
                " *   with the License. You may obtain a copy of the License at\n".
                " *   http://www.qualityunit.com/licenses/gpf\n".
                " *   Generated on: " . Gpf_Common_DateUtils::getDateTime(time()) . "\n" .
                " *   PAP version: " . Pap_Application::getInstance()->getVersion() . ", GPF version: " . Gpf::GPF_VERSION . "\n" .
                " *   \n".
                " */\n\n".
                "@ini_set('session.gc_maxlifetime', 28800);\n".
                "@ini_set('session.cookie_path', '/');\n".
                "@ini_set('session.use_cookies', true);\n".
                "@ini_set('magic_quotes_runtime', false);\n".
                "@ini_set('session.use_trans_sid', false);\n".
                "@ini_set('zend.ze1_compatibility_mode', false);\n";
	}

    protected function addAdditionalClasses() {
        $this->addGpfObject();
    }
    
    protected function addExtraClasses() {
        $this->addHttpClient();        
    }

	private function addGpfObject() {
		fwrite($this->output, "
if (!class_exists('Gpf', false)) {
    class Gpf {
        const YES = 'Y';
        const NO = 'N';
    }
}

if (!class_exists('Gpf_Object', false)) {		
    class Gpf_Object {
        protected function createDatabase() {
            return Gpf_DbEngine_Database::getDatabase();
        }
    
        public function _(\$message) {
            return \$message;
        }
    
        public function _localize(\$message) {
            return \$message;
        }
    
        public function _sys(\$message) {
            return \$message;
        }
    }
}
");
	}


    private function addHttpClient() {
        fwrite($this->output, "
if (!class_exists('Gpf_Net_Http_Client', false)) {
    class Gpf_Net_Http_Client extends Gpf_Net_Http_ClientBase {

        protected function isNetworkingEnabled() {
            return true;
        }

        protected function setProxyServer(Gpf_Net_Http_Request \$request) {
        }
    }
}
");
    }
}
?>
