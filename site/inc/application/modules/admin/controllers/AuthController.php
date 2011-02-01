<?php

/**
 * AuthController
 *
 * @author michiel
 * @version
 */

require_once 'Zend/Controller/Action.php';

class Admin_AuthController extends Zend_Controller_Action {

    function init() {
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function loginAction() {
        $baseUrl = $this->view->baseUrl();
        $this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/LoginPanel.js' );
        $this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/login.js' );
    }

    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('admin/');
    }

    public function authenticateAction() {
        $this->_helper->layout()->disableLayout();

        $request = $this->getRequest();
        $service_auth = new Admin_Service_Authenticate();

        if($service_auth->login($request)) {
        //redirect the user to admin interface (redirect via javascript)
            $result = array(
                'success' => true
            );
        }else {
            $result = array(
                'success' => false
            );
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

}

