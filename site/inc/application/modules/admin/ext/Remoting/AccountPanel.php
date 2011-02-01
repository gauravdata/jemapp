<?php

class Admin_Ext_Remoting_AccountPanel
{
    /**
     * @remotable
     * @formHandler
     */
    public function submit($post, $files)
    {
	try {
	    $Account = new Admin_Model_Account($post);
	    $AccountSrv = new Admin_Service_Account();
	    $account_id = $AccountSrv->set($Account);

	    $Account = $AccountSrv->get($account_id);

	    $result = new stdClass();
	    $result->success = true;
	    $result->data = $Account->getData();
	}
	catch (Twm_Form_Exception $ex)
	{
	    $result->success = false;
	    $result->errors = $ex->getExtDirectErrors();
	}
	catch (Exception $ex)
	{
	    $result->success = false;
	    $result->errors = $ex->getMessage();
	}
	return $result;


    }

    /**
     * @remotable
     */
    public function load($params)
    {
	$account_id = $params->account_id;
	$AccountSrv = new Admin_Service_Account();
	$Account = $AccountSrv->get($account_id);

	$result = new stdClass();
	$result->success = true;
	$result->data = $Account->getData();
	return $result;
    }
}