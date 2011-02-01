<?php
class Admin_Ext_Remoting_Base {
	/**
	 * @remotable
         * @formHandler
	 */
	public function login($post, $files) {

            $AuthSrv = new Admin_Service_Authenticate();
            $o->success = $AuthSrv->login($post['username'], $post['password']);

            if (!$o->success)
            {
                $o->errors = new stdClass();
                $o->errors->username = 'Invalid credentials';
                $o->errors->password = 'Invalid credentials';
            }

            return $o;
	}

        public function logout(){
            $auth = new Admin_Service_Authenticate();
            return $auth->logout();
        }

        /**
         * @remotable
         */
        public function deleteAccount($account_id)
        {
            $AccountSrv = new Admin_Service_Account();
	    return $AccountSrv->delete($account_id);
        }
}