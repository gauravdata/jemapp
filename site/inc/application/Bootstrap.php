<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

/**
 * Add the config to the registry
 */
    protected function _initConfig()
    {
	Zend_Registry::set ( 'config', $this->getOptions () );
    }

    /**
     * Configure the default modules autoloading, here we first create
     * a new module autoloader specifiying the base path and namespace
     * for our default module. This will automatically add the default
     * resource types for us. We also add two custom resources for Services
     * and Model Resources.
     */
    protected function _initDefaultModuleAutoloader()
    {
	$this->_resourceLoader = new Zend_Application_Module_Autoloader ( array ('namespace' => '', 'basePath' => APPLICATION_PATH . '/modules/default' ) );
	$this->_resourceLoader->addResourceTypes (
	    array (
	    'modelResource' => array ('path' => 'models', 'namespace' => 'Model' ),
	    'serviceResource' => array ('path' => 'services', 'namespace' => 'Service' ),
	    'tableResource' => array ('path' => 'data/DbTable', 'namespace' => 'DbTable' )
	    )
	);
    }

    protected function _initViewHelpers()
    {
	$this->bootstrap('view');
	$view = $this->getResource('view');
	$view->addHelperPath("App/View/Helper", "App_View_Helper");
    }

    /**
     * Setup request and response so we can use Firebug for logging
     * also make the dispatcher prefix the default module
     */
    protected function _initFrontControllerSettings()
    {
	$this->bootstrap ( 'frontController' );
	$this->frontController->setResponse ( new Zend_Controller_Response_Http ( ) );
	$this->frontController->setRequest ( new Zend_Controller_Request_Http ( ) );
    }

}