<?php

class Core_Model_DbTable_Carts extends HTS_Db_Table
{
	protected $_name = 'carts';
	protected $_rowClass = 'Core_Model_Cart';

	public function findAllByIdBuyer($id_buyer)
	{
		return $this->fetchAll('id_buyer = ' . $this->_db->quote($id_buyer));
	}
}
