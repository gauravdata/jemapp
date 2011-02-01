<?php
class Twm_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
	
    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $module = $request->getModuleName();
        $action = $request->getActionName();
        if ($module != 'admin' || $action == 'api' || $action == 'router')
        {
            return;
        }

        $auth = Zend_Auth::getInstance ();

	if (isset($_POST['auth']))
	{
	    if ($this->_request->isPost ())
	    {
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance ();
		$authAdapter = new Zend_Auth_Adapter_DbTable(
		    $dbAdapter,
		    'account',
		    'username',
		    'password',
		    "MD5(?)"
		);

		$authAdapter->setIdentity($_POST['auth']['username'])->setCredential($_POST['auth']['password']);

		$result = $auth->authenticate ( $authAdapter );
		if ($result->isValid ())
		{
		    $storage = $auth->getStorage();
		    $storage->write($authAdapter->getResultRowObject(
			null,
			'password'
		    ));
		}
		else
		{
		    $errors = $result->getMessages();
		    Zend_Debug::dump($errors);
		}
	    }
	}

        if (! $auth->hasIdentity ()) {
            $request->setModuleName($request->getModuleName());
            $request->setControllerName('auth');
            $request->setActionName('login');
        }
    }
}