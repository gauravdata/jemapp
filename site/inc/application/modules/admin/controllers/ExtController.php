<?php

/**
 * ExtController
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';

class Admin_ExtController extends Zend_Controller_Action
{

    protected $session = null;

    public function init()
    {

	$base = new Admin_Ext_Remoting_Base();
	$this->session = new Zend_Session_Namespace("admin", true);

	$this->_helper->layout()->disableLayout();
	$this->_helper->viewRenderer->setNoRender();
    }

    public function apiAction()
    {
    // Include ExtDirect PHP Helpers
	require_once('ExtDirect/API.php');
	require_once('ExtDirect/CacheProvider.php');

	$cache = new ExtDirect_CacheProvider('cache/api_cache.txt');
	$api = new ExtDirect_API();

	$api->setRouterUrl($this->view->baseUrl().'/admin/ext/router'); // default
	//$api->setCacheProvider($cache);
	$api->setNamespace('Admin.Remoting');
	$api->setDescriptor('Admin.Remoting.REMOTING_API');
	$api->setDefaults(array(
	    'autoInclude' => false // using autoloader of zend
	));

	$api->add(
	    array(
	    'Base' => array('prefix' => 'Admin_Ext_Remoting_'),
	    'AccountGridPanel' => array('prefix' => 'Admin_Ext_Remoting_'),
	    'AccountPanel' => array('prefix' => 'Admin_Ext_Remoting_')
	    )
	);

	$api->output();

	$this->session->{'ext-direct-state'} = $api->getState();
    }

    public function routerAction()
    {
	require_once('ExtDirect/API.php');
	require_once('ExtDirect/Router.php');
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	// this should alwasy be set but if its not, then execute api.php without outputting it
	if(!isset($this->session->{'ext-direct-state'}))
	{
	    ob_start();
	    include('api.php');
	    ob_end_clean();
	}

	$api = new ExtDirect_API();
	$api->setState($this->session->{'ext-direct-state'});

	$router = new ExtDirect_Router($api);
	$router->dispatch();
	$router->getResponse(true); // true to print the response instantly
    }
}