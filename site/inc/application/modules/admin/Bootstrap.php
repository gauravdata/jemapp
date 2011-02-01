<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initDefaultModuleAutoloader()
    {
	$this->_resourceLoader = new Zend_Application_Module_Autoloader ( array ('namespace' => 'Admin', 'basePath' => APPLICATION_PATH . '/modules/admin' ) );
	$this->_resourceLoader->addResourceTypes (
	    array (
	    'modelResource' => array ('path' => 'models', 'namespace' => 'Model' ),
	    'serviceResource' => array ('path' => 'services', 'namespace' => 'Service' ),
	    'extResource' => array ('path' => 'ext', 'namespace' => 'Ext' ),
	    'tableResource' => array ('path' => 'data/DbTable', 'namespace' => 'Data_DbTable' )
	    )
	);
    }
}