<?php
class Twm_Controller_Plugin_ModuleSwitcher extends Zend_Controller_Plugin_Abstract
{
    protected $_view = null;
 
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $moduleName = $request->getModuleName();
 
        Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();  
        $layout->setLayoutPath('../inc/application/modules/' . $moduleName . '/layouts')->setLayout($moduleName);
 
        $view = new Zend_View();
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->init();      
 
        $this->_view = $viewRenderer->view;
        $this->_view->headMeta()->setHttpEquiv('content-type', 'text/html; charset=utf-8');
        return $request;
    }
}