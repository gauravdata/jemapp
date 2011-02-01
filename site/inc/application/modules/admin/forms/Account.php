<?php
class Admin_Form_Account extends Twm_Form_DbTable_Abstract
{
    function init()
    {
	parent::init();

	$username = $this->getElement('username');
	$username->addValidator('alnum');
    }

    function isValid($data)
    {
	if (!isset($data['account_id']) || empty($data['account_id']))
	{
	    $validatorUsernameUnique = new Twm_Validate_UsernameUnique('account','username');
	}
	if (isset($data['account_id']) && !empty($data['account_id']))
	{
	    $where = Zend_Db_Table::getDefaultAdapter()->quoteInto('account_id != ?', $data['account_id']);
	    $validatorUsernameUnique = new Twm_Validate_UsernameUnique('account','username', $where);
	}
	$username = $this->getElement('username');
	$username->addValidator($validatorUsernameUnique);

	if (!isset($data['password']) || empty($data['password']))
	{
	    $password = $this->getElement('password');
	    $password->setRequired(false);
	    $password->setAllowEmpty(true);
	}

	return parent::isValid($data);
    }
}