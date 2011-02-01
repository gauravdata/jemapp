<?php

abstract class Admin_Data_DbTable_Abstract extends Zend_Db_Table_Abstract
{

    private function filter($data)
    {
	$cols = $this->info(self::COLS);
	foreach (array_keys($data) as $key)
	{
	    if (!in_array($key, $cols))
	    {
		unset($data[$key]);
	    }
	}
	return $data;
    }

    private function getPrimary()
    {
	$key = $this->info(self::PRIMARY);
	return $key[$this->_identity];
    }

    public function insert(array $data)
    {
	$data = $this->filter($data);
	if (count($data) <= 0) return false;
	return parent::insert($data);
    }

    public function update(array $data, $where)
    {
	$data = $this->filter($data);
	if (count($data) <= 0) return false;
	return parent::update($data, $where);
    }

    public function set($data)
    {
	if (null == $this->_primary)
	{
	    throw new Twm_Cms_DbTable_Exception('Unknown primary key');
	}
	$primary = $this->getPrimary();
	if (isset($data[$primary]) && !empty($data[$primary]))
	{
	    $where = $this->getDefaultAdapter()->quoteInto("{$primary} = ?", $data[$primary]);
	    $this->update($data, $where);
	    return $data[$primary];
	}
	else
	{
	    $this->insert($data);
	    return $this->getAdapter()->lastInsertId();
	}
    }
}