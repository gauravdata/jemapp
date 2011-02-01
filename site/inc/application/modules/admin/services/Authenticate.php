<?php

class Admin_Service_Authenticate {

    public function login($username, $password = null) {

        if ($username instanceof Zend_Controller_Request_Http) {
            $request = $username;
            //collect username and password from the request object
            $username = $request->getParam('username');
            $password = $request->getParam('password');
        }

        // setup Zend_Auth adapter for a database table
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable ( $db );
        $authAdapter->setTableName ( 'account' );
        $authAdapter->setIdentityColumn ( 'username' );
        $authAdapter->setCredentialColumn ( 'password' );

        // Set the input credential values to authenticate against
        $authAdapter->setIdentity ( $username );
        $authAdapter->setCredential ( md5($password) );

        // do the authentication
        $auth = Zend_Auth::getInstance ();
        $result = $auth->authenticate ( $authAdapter );
        if ($result->isValid ()) {
        // success : store database row to auth's storage system
        // (not the password though!)
            $data = $authAdapter->getResultRowObject ( null, 'password' );
            $auth->getStorage ()->write ( $data );

            //user logged in
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
    }
}
