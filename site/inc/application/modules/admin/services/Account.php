<?php

class Admin_Service_Account
{

/**
 * returns array of Admin_Model_Account
 *
 * @return array
 */
    public function getAll()
    {

	$accounts = array();

	$AccountTbl = new Admin_Data_DbTable_Account();
	$select = $AccountTbl->select();
	$result = $AccountTbl->fetchAll($select);
	foreach ($result as $row)
	{
	    $Account = new Admin_Model_Account();
	    $Account->setData($row->toArray());
	    $accounts[]= $Account;
	}
	return $accounts;
    }

    /**
     *
     * @param int $account_id
     * @return Admin_Model_Account
     */
    public function get($account_id)
    {
	$AccountTbl = new Admin_Data_DbTable_Account();
	$select = $AccountTbl->select();
	$select->where('account_id = ?', $account_id);
	$row = $AccountTbl->fetchRow($select);
	if ($row !== null)
	{
	    unset($row->password);
	    return new Admin_Model_Account($row->toArray());
	}
	return null;
    }

    public function set(Admin_Model_Account $Account)
    {
	$data = $Account->getData();
	$AccountTbl = new Admin_Data_DbTable_Account();
	$form = new Admin_Form_Account($AccountTbl);
	$valid = $form->isValid($data);
	if (!$valid)
	{
	    throw new Twm_Form_Exception($form);
	}
	return $AccountTbl->set($data);
    }

    /**
     * delete account(s) by id or by a array of id's
     *
     * return the number of deleted accounts. valid values are 0 and 1
     *
     * @param mixed $account_id
     * @return int
     */
    public function delete($account_id)
    {
	$numDeleted = 0;
	$AccountTbl = new Admin_Data_DbTable_Account();
	if (!is_array($account_id))
	{
	    $account_id[] = $account_id;
	}
	foreach ($account_id as $id)
	{
	    $where = Zend_Db_Table::getDefaultAdapter()->quoteInto('account_id = ?', $id);
	    $numDeleted += $AccountTbl->delete($where);
	}

	return $numDeleted;
    }

    public function usernameUnique($username, $account_id)
    {
	$AccountTbl = new Admin_Data_DbTable_Account();
	$select = $AccountTbl->select()->where('username = ?', $username);
	$select->where('account_id != ?', $account_id);
	$row = $AccountTbl->fetchRow($select);
	return ($row === null);
    }
}
