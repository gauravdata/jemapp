<?php

class Admin_Ext_Remoting_AccountGridPanel
{
/**
 * @remotable
 */
    public function read($config)
    {
	$AccountSrv = new Admin_Service_Account();
	$accounts = $AccountSrv->getAll();

	$result = new stdClass();
	$result->data = array();
	$i = 1;
	foreach ($accounts as $Account)
	{
	    $result->data[] = $Account->getData();
	}
	$result->success = true;
	$result->total = count($accounts);
	return $result;
    }
}