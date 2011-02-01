<?php
class Admin_Model_Account extends Admin_Model_Abstract
{
    function setPassword($password)
    {
	// if new password
	if (strlen($password) > 0 && strlen($password) < 32)
	{
	    $this->_data['password'] = md5($password);
	}
    }
}