<?php

/**
 * IndexController - The default controller class
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';

class Admin_IndexController extends Zend_Controller_Action {

    /**
     * The default action - show the home page
     */
    public function indexAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $baseUrl = $this->view->baseUrl();
	$this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/vtypes.js' );
	$this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/ApplicationPanel.js' );
	$this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/Window.js' );
        $this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/AdminPanel.js' );
	$this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/AccountPanel.js' );
	$this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/AccountGridPanel.js' );
	$this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/AccountWindow.js' );
	$this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/toolbar/SaveCloseToolbar.js' );
	$this->view->headScript()->appendFile ( $baseUrl . '/admin/scripts/boot.js' );
    }

    public function headerAction() {}
}
